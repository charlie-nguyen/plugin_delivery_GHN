<?php
/**
 * Author: lqdung1992@gmail.com
 * Date: 1/31/2019
 * Time: 2:42 PM
 */

namespace Plugin\OSGHNDelivery\Controller\Front\Shopping;

use Eccube\Controller\AbstractController;
use Eccube\Entity\Master\OrderItemType;
use Eccube\Entity\Master\TaxDisplayType;
use Eccube\Entity\Master\TaxType;
use Eccube\Entity\OrderItem;
use Eccube\Entity\Shipping;
use Eccube\Repository\DeliveryRepository;
use Eccube\Repository\Master\OrderItemTypeRepository;
use Plugin\OSGHNDelivery\Entity\GHNService;
use Plugin\OSGHNDelivery\Entity\GHNWarehouse;
use Plugin\OSGHNDelivery\Form\Type\Front\GHNDeliveryShoppingType;
use Plugin\OSGHNDelivery\Repository\GHNConfigRepository;
use Plugin\OSGHNDelivery\Repository\GHNDeliveryRepository;
use Plugin\OSGHNDelivery\Repository\GHNServiceRepository;
use Plugin\OSGHNDelivery\Repository\GHNWarehouseRepository;
use Plugin\OSGHNDelivery\Service\ApiService;
use Plugin\OSGHNDelivery\Service\PurchaseFlow\GHNProcessor;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class GHNShoppingController
 * @package Plugin\OSGHNDelivery\Controller\Front\Shopping
 */
class GHNShoppingController extends AbstractController
{
    /**
     * @var GHNWarehouseRepository
     */
    protected $warehouseRepo;

    /**
     * @var ApiService
     */
    protected $apiService;

    /**
     * @var GHNServiceRepository
     */
    protected $serviceRepo;

    /**
     * @var GHNConfigRepository
     */
    protected $configRepo;

    /**
     * @var OrderItemTypeRepository
     */
    protected $orderItemTypeRepository;

    /**
     * @var GHNDeliveryRepository
     */
    protected $ghnDeliveryRepo;

    /**
     * @var DeliveryRepository
     */
    protected $deliveryRepository;

    /**
     * GHNShoppingController constructor.
     * @param GHNWarehouseRepository $warehouseRepo
     * @param ApiService $apiService
     * @param GHNServiceRepository $serviceRepo
     * @param GHNConfigRepository $configRepo
     * @param OrderItemTypeRepository $orderItemTypeRepository
     * @param GHNDeliveryRepository $ghnDeliveryRepo
     * @param DeliveryRepository $deliveryRepository
     */
    public function __construct(GHNWarehouseRepository $warehouseRepo, ApiService $apiService, GHNServiceRepository $serviceRepo, GHNConfigRepository $configRepo, OrderItemTypeRepository $orderItemTypeRepository, GHNDeliveryRepository $ghnDeliveryRepo, DeliveryRepository $deliveryRepository)
    {
        $this->warehouseRepo = $warehouseRepo;
        $this->apiService = $apiService;
        $this->serviceRepo = $serviceRepo;
        $this->configRepo = $configRepo;
        $this->orderItemTypeRepository = $orderItemTypeRepository;
        $this->ghnDeliveryRepo = $ghnDeliveryRepo;
        $this->deliveryRepository = $deliveryRepository;
    }

    /**
     * @param Request $request
     * @param Shipping $Shipping
     * @return array|RedirectResponse
     *
     * @Route(path="/shopping/ghn/delivery/{id}", name="ghn_delivery_shopping", requirements={"id"="\d+"})
     * @ParamConverter("Shipping")
     * @Template("@OSGHNDelivery/front/Shopping/delivery.twig")
     */
    public function index(Request $request, Shipping $Shipping)
    {
        /** @var GHNService $service */
        $service = $this->serviceRepo->findOneBy(['Shipping' => $Shipping]);
        if (!$service) {
            $service = new GHNService();
            $service->setShipping($Shipping);
        }

        $builder = $this->formFactory->createBuilder(GHNDeliveryShoppingType::class, $Shipping, ['Pref' => $Shipping->getPref()]);

        $form = $builder->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $service->setMainServiceId($form['main_service_id']->getData());
            // get service ID + fee
            $warehouse = $this->warehouseRepo->getOne();
            $fromGHNPref = $warehouse->getGHNPref();
            $service->setFromPref($fromGHNPref);

            $toGHNPref = $Shipping->getGHNPref();
            $service->setToPref($toGHNPref);

            $data = $request->getSession()->get($this->eccubeConfig->get('ghn_session_service_fee'));
            // remove session
            $request->getSession()->remove($this->eccubeConfig->get('ghn_session_service_fee'));
            if (is_null($data)) {
                $output = $this->apiService->findAvailableServices($fromGHNPref->getDistrictId(), $toGHNPref->getDistrictId());
                // call api to get service list
                $data = $output->getData();
                // error
                if (empty($data) || !is_array($data)) {
                    $form['main_service_id']->addError(new FormError('ghn.shopping.delivery.service_connection'));

                    return [
                        'form' => $form->createView(),
                        'Shipping' => $Shipping,
                    ];
                }
            }

            if (!$service->setMainService($data)) {
                $form['main_service_id']->addError(new FormError('ghn.shopping.delivery.service_incorrect'));

                return [
                    'form' => $form->createView(),
                    'Shipping' => $Shipping,
                ];
            }

            $service->setWeight($this->configRepo->find(1)->getWeight());

            // remove old order item
            /** @var OrderItem $orderItem */
            foreach ($Shipping->getOrderItems() as $orderItem) {
                if ($orderItem->isDeliveryFee()) {
                    $Shipping->removeOrderItem($orderItem);
                    $Shipping->getOrder()->removeOrderItem($orderItem);
                    $this->entityManager->remove($orderItem);
                }
            }
            // add new to Order Item
            $this->addGHNOrderItem($Shipping, $service);

            $this->entityManager->persist($service);
            $this->entityManager->flush();

            // check still redirect?
            $redirects = $this->removeRedirectSession($Shipping);

            $nextValue = reset($redirects);
            if (!empty($redirects) && $nextValue) {
                $nextShippingId = key($redirects);

                return $this->redirectToRoute('ghn_delivery_shopping', ['id' => $nextShippingId]);
            }

            return $this->redirectToRoute('shopping');
        }

        return [
            'form' => $form->createView(),
            'Shipping' => $Shipping,
        ];
    }

    /**
     * @param Request $request
     * @param Shipping $shipping
     *
     * @Route(path="/shopping/ghn/remove/{id}", name="ghn_delivery_remove", requirements={"id"="\d+"}, methods={"POST"})
     * @ParamConverter("Shipping")
     */
    public function removeGHNDeliveryFromOrder(Request $request, Shipping $shipping)
    {
        $this->isTokenValid();

        // current delivery
        $currentDelivery = $shipping->getDelivery();

        $isChange = false;
        // is GHN delivery?
        $ghnDelivery = $this->ghnDeliveryRepo->find($currentDelivery);
        if ($ghnDelivery) {
            $OrderItems = $shipping->getProductOrderItems();
            $SaleTypes = [];
            foreach ($OrderItems as $OrderItem) {
                $ProductClass = $OrderItem->getProductClass();
                $SaleType = $ProductClass->getSaleType();
                $SaleTypes[$SaleType->getId()] = $SaleType;
            }
            $Deliveries = $this->deliveryRepository->getDeliveries($SaleTypes);
            foreach ($Deliveries as $delivery) {
                if ($delivery->getId() != $currentDelivery->getId()) {
                    $shipping->setDelivery($delivery);
                    $this->entityManager->flush($shipping);
                    $isChange = true;
                    break;
                }
            }

            if ($isChange) {
                $this->removeRedirectSession($shipping);

                return $this->redirectToRoute('shopping');
            }
        }

        $this->addError('ghn.shopping.delivery.remove.error');

        return $this->redirectToRoute('ghn_delivery_shopping', ['id' => $shipping->getId()]);
    }

    /**
     * ajax
     *
     * @param Request $request
     * @param Shipping $Shipping
     * @return array
     *
     * @Route(path="/shopping/ghn/service/{id}", name="ghn_delivery_service_fee", requirements={"id"="\d+"}, methods={"POST"})
     * @ParamConverter("Shipping")
     * @Template("@OSGHNDelivery/front/Shopping/service_ajax.twig")
     */
    public function getServiceAndFee(Request $request, Shipping $Shipping)
    {
        if(!$request->isXmlHttpRequest()) {
            throw new NotFoundHttpException();
        }

        $this->isTokenValid();

        $builder = $this->formFactory->createBuilder(GHNDeliveryShoppingType::class, $Shipping, ['Pref' => $Shipping->getPref()]);
        $builder->remove('main_service_id');
        $form = $builder->getForm();
        $form->handleRequest($request);

        $data = [];
        $message = null;
        if ($form->isValid()) {
            // from main warehouse
            /** @var GHNWarehouse $warehouse */
            $warehouse = $this->warehouseRepo->getOne();
            $fromDistrict = (int) $warehouse->getGHNPref()->getDistrictId();
            $toDistrict = (int) $Shipping->getGHNPref()->getDistrictId();
            $weight = $this->configRepo->find(1)->getWeight();

            // call api to get service list
            $output = $this->apiService->findAvailableServices($fromDistrict, $toDistrict, ['Weight' => $weight]);
            $data = $output->getData();

            $request->getSession()->set($this->eccubeConfig->get('ghn_session_service_fee'), $data);
            $message = $output->getMsg();
        }

        return ['ghn_service' => $data, 'message' => $message];
    }

    /**
     * @param Shipping $Shipping
     * @return mixed
     */
    private function removeRedirectSession(Shipping $Shipping)
    {
        $redirects = $this->session->get($this->eccubeConfig->get('ghn_session_redirect'), [$Shipping->getId() => true]);
        unset($redirects[$Shipping->getId()]);
        $this->session->set($this->eccubeConfig->get('ghn_session_redirect'), $redirects);

        return $redirects;
    }

    /**
     * @param Shipping $Shipping
     * @param $service
     */
    private function addGHNOrderItem(Shipping $Shipping, GHNService $service)
    {
        $type = $this->orderItemTypeRepository->find(OrderItemType::DELIVERY_FEE);
        $TaxInclude = $this->entityManager
            ->find(TaxDisplayType::class, TaxDisplayType::INCLUDED);
        $Taxation = $this->entityManager
            ->find(TaxType::class, TaxType::TAXATION);

        $OrderItem = new OrderItem();
        $OrderItem->setProductName(trans('ghn.delivery.order_item.name'))
            ->setPrice($service->getMainServiceFee())
            ->setQuantity(1)
            ->setOrderItemType($type)
            ->setShipping($Shipping)
            ->setOrder($Shipping->getOrder())
            ->setTaxDisplayType($TaxInclude)
            ->setTaxType($Taxation)
            ->setProcessorName(GHNProcessor::class);
        $this->entityManager->persist($OrderItem);
        $service->setOrderItem($OrderItem);

        $Shipping->addOrderItem($OrderItem);
        $Shipping->getOrder()->addItem($OrderItem);
        $Shipping->setGHNService($service);
    }
}
