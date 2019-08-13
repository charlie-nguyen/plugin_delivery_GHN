<?php

namespace Plugin\OSGHNDelivery;

use Doctrine\ORM\EntityManagerInterface;
use Eccube\Common\EccubeConfig;
use Eccube\Entity\Master\OrderStatus;
use Eccube\Entity\Order;
use Eccube\Entity\OrderItem;
use Eccube\Entity\Shipping;
use Eccube\Event\EccubeEvents;
use Eccube\Event\EventArgs;
use Eccube\Event\TemplateEvent;
use Plugin\OSGHNDelivery\Entity\GHNConfig;
use Plugin\OSGHNDelivery\Repository\GHNConfigRepository;
use Plugin\OSGHNDelivery\Repository\GHNDeliveryRepository;
use Plugin\OSGHNDelivery\Repository\GHNOrderRepository;
use Plugin\OSGHNDelivery\Repository\GHNServiceRepository;
use Plugin\OSGHNDelivery\Repository\GHNWarehouseRepository;
use Plugin\OSGHNDelivery\Service\ApiService;
use Plugin\OSGHNDelivery\Service\PurchaseFlow\GHNProcessor;
use Symfony\Bundle\FrameworkBundle\Controller\ControllerTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Session;

class Event implements EventSubscriberInterface
{
    use ControllerTrait;

    /** @var Session */
    protected $session;
    /** @var ContainerInterface */
    protected $container;
    /** @var EccubeConfig */
    protected $eccubeConfig;

    /** @var GHNDeliveryRepository */
    protected $ghnDeliveryRepo;

    /** @var ApiService */
    protected $apiService;

    /** @var GHNWarehouseRepository */
    protected $warehouseRepo;

    /** @var GHNOrderRepository */
    protected $ghnOrderRepo;

    /** @var GHNConfigRepository */
    protected $configRepo;

    /** @var EntityManagerInterface */
    protected $entityManager;

    /** @var GHNServiceRepository */
    protected $serviceRepo;

    /**
     * @var RequestStack
     */
    protected $request;

    /**
     * Event constructor.
     * @param Session $session
     * @param ContainerInterface $container
     * @param EccubeConfig $eccubeConfig
     * @param GHNDeliveryRepository $GHNDeliveryRepo
     * @param ApiService $apiService
     * @param GHNWarehouseRepository $warehouseRepo
     * @param GHNOrderRepository $GHNOrderRepo
     * @param GHNConfigRepository $configRepo
     * @param EntityManagerInterface $entityManager
     * @param GHNServiceRepository $serviceRepo
     * @param RequestStack $request
     */
    public function __construct(Session $session, ContainerInterface $container, EccubeConfig $eccubeConfig, GHNDeliveryRepository $GHNDeliveryRepo, ApiService $apiService, GHNWarehouseRepository $warehouseRepo, GHNOrderRepository $GHNOrderRepo, GHNConfigRepository $configRepo, EntityManagerInterface $entityManager, GHNServiceRepository $serviceRepo, RequestStack $request)
    {
        $this->session = $session;
        $this->container = $container;
        $this->eccubeConfig = $eccubeConfig;
        $this->ghnDeliveryRepo = $GHNDeliveryRepo;
        $this->apiService = $apiService;
        $this->warehouseRepo = $warehouseRepo;
        $this->ghnOrderRepo = $GHNOrderRepo;
        $this->configRepo = $configRepo;
        $this->entityManager = $entityManager;
        $this->serviceRepo = $serviceRepo;
        $this->request = $request;
    }


    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            'Shopping/index.twig' => 'shoppingIndex',
            EccubeEvents::ADMIN_ORDER_EDIT_INDEX_COMPLETE => 'orderEditHandle',
            EccubeEvents::ADMIN_ORDER_UPDATE_STATUS_COMPLETE => 'createGHNOrderForOneShipping',
            EccubeEvents::ADMIN_ORDER_EDIT_INDEX_PROGRESS => 'calcGHNServeFeeOneShipping',
            EccubeEvents::ADMIN_SHIPPING_EDIT_INDEX_COMPLETE => 'calcGHNServeFeeMultiShipping',
            '@admin/Order/edit.twig' => 'renderOrder'
        ];
    }

    public function renderOrder(TemplateEvent $event)
    {
        $order = $event->getParameter('Order');
        $ghnOrder = $this->ghnOrderRepo->findBy(['Order' => $order]);
        $event->setParameter('GHNOrders', $ghnOrder);
        $event->addSnippet('@OSGHNDelivery/admin/render_order.twig');
    }

    public function calcGHNServeFeeMultiShipping(EventArgs $eventArgs)
    {
        $config = $this->configRepo->find(1);
        if (!$config) {
            return;
        }
        /** @var Shipping[] $targetShippings */
        $targetShippings = $eventArgs['TargetShippings'];

        $request = $this->request->getCurrentRequest();
        $dataRequest = $request->get('form')['shippings'];

        foreach ($targetShippings as $key => $shipping) {
            $mainServiceId = $dataRequest[$key]['main_service_id'];
            $this->serviceRepo->createGHNServiceByShipping($shipping, $mainServiceId, $key);
        }
    }

    /**
     * For one shipping only
     *
     * @param EventArgs $eventArgs
     * @throws \Doctrine\ORM\ORMException
     */
    public function calcGHNServeFeeOneShipping(EventArgs $eventArgs)
    {
        $config = $this->configRepo->find(1);
        if (!$config) {
            return;
        }
        /** @var Order $order */
        $order = $eventArgs['TargetOrder'];

        if ($order->isMultiple()) {
            return;
        }
        $requestData = $this->request->getCurrentRequest()->get('order');
        if (!isset($requestData['Shipping']['main_service_id']) || !is_numeric($requestData['Shipping']['main_service_id'])) {
            $this->addFlash('eccube.admin.error', 'ghn.shopping.delivery.service_incorrect');
            return;
        }

        /** @var Shipping $shp */
        $shp = $order->getShippings()->first();
        $mainServiceId = $requestData['Shipping']['main_service_id'];
        $this->serviceRepo->createGHNServiceByShipping($shp, $mainServiceId);
        // do not flush
    }

    public function createGHNOrderForOneShipping(EventArgs $event)
    {
        /** @var GHNConfig $config */
        $config = $this->configRepo->find(1);
        if (!$config) {
            return;
        }
        /** @var Shipping $shipping */
        $shipping = $event['Shipping'];
        /** @var Order $order */
        $order = $shipping->getOrder();

        if ($order->getOrderStatus()->getId() == OrderStatus::CANCEL) {
            // do cancel
            $this->ghnOrderRepo->cancelGHNOrderByShipping($shipping);
            $this->entityManager->flush();
        }

        if ($order->getOrderStatus()->getId() != OrderStatus::IN_PROGRESS) {
            return;
        }
        $this->ghnOrderRepo->createGHNOrderByShipping($shipping);

        // please flush all for update order
        $this->entityManager->flush();
    }

    /**
     * @param EventArgs $event
     * @throws \Doctrine\ORM\ORMException
     */
    public function orderEditHandle(EventArgs $event)
    {
        /** @var GHNConfig $config */
        $config = $this->configRepo->find(1);
        if (!$config) {
            return;
        }
        /** @var Order $order */
        $order = $event['TargetOrder'];

        if ($order->getOrderStatus()->getId() == OrderStatus::CANCEL) {
            // do cancel
            $this->ghnOrderRepo->cancelGHNOrderByOrder($order);
            $this->addFlash('eccube.admin.success', 'ghn.shipping.cancel.success');
            $this->entityManager->flush();
            return;
        }

        if ($order->getOrderStatus()->getId() != OrderStatus::IN_PROGRESS) {
            return;
        }

        $this->ghnOrderRepo->createGHNOrderByOrder($order);
        $this->addFlash('eccube.admin.success', 'ghn.shipping.create.success');

        // please flush all for update order
        $this->entityManager->flush();
    }

    /**
     * @param TemplateEvent $templateEvent
     */
    public function shoppingIndex(TemplateEvent $templateEvent)
    {
        $config = $this->configRepo->find(1);
        if (!$config) {
            return;
        }

        $parameters = $templateEvent->getParameters();
        /** @var Order $order */
        $order = $parameters['Order'];
        $redirects = [];
        // check GHN delivery in order
        foreach ($order->getShippings() as $shipping) {
            $redirects[$shipping->getId()] = false;
            $delivery = $shipping->getDelivery();
            $GHNDelivery = $this->ghnDeliveryRepo->find($delivery->getId());
            // if ghn delivery => add script to redirect to GHN page
            if ($GHNDelivery) {
                $redirects[$shipping->getId()] = true;
                /** @var OrderItem $shippingOrderItem */
                foreach ($shipping->getOrderItems() as $shippingOrderItem) {
                    // is exist GHN delivery fee
                    if ($shippingOrderItem->isDeliveryFee() && $shippingOrderItem->getProcessorName() == GHNProcessor::class) {
                        $redirects[$shipping->getId()] = false;
                    }
                }
            }
        }

        // save to session
        $this->session->set($this->eccubeConfig->get('ghn_session_redirect'), $redirects);

        // change tempalte
        foreach ($redirects as $key => $value) {
            if ($value) {
                $templateEvent->setResponse($this->redirectToRoute('ghn_delivery_shopping', ['id' => $key]));
                $templateEvent->setSource($templateEvent->getResponse()->getContent());
                break;
            }
        }
    }
}
