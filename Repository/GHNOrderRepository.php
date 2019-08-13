<?php
/**
 * Author: lqdung1992@gmail.com
 * Date: 2/15/2019
 * Time: 4:27 PM
 */

namespace Plugin\OSGHNDelivery\Repository;

use Doctrine\Common\Persistence\ManagerRegistry;
use Eccube\Common\EccubeConfig;
use Eccube\Entity\Order;
use Eccube\Entity\Shipping;
use Eccube\Repository\AbstractRepository;
use Plugin\OSGHNDelivery\Entity\GHNOrder;
use Plugin\OSGHNDelivery\Entity\GHNOrderStatus;
use Plugin\OSGHNDelivery\Service\ApiParserService;
use Plugin\OSGHNDelivery\Service\ApiService;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Class GHNOrderRepository
 * @package Plugin\OSGHNDelivery\Repository
 */
class GHNOrderRepository extends AbstractRepository
{
    /** @var Session */
    protected $session;

    /** @var ApiService */
    protected $apiService;

    /** @var GHNWarehouseRepository */
    protected $warehouseRepo;

    /** @var GHNDeliveryRepository */
    protected $ghnDeliveryRepo;

    /** @var GHNServiceRepository */
    protected $serviceRepo;

    /**
     * GHNOrderRepository constructor.
     * @param ManagerRegistry $registry
     * @param Session $session
     * @param ApiService $apiService
     * @param GHNWarehouseRepository $warehouseRepo
     * @param GHNDeliveryRepository $ghnDeliveryRepo
     * @param GHNServiceRepository $serviceRepo
     * @param EccubeConfig $eccubeConfig
     */
    public function __construct(ManagerRegistry $registry, Session $session, ApiService $apiService, GHNWarehouseRepository $warehouseRepo, GHNDeliveryRepository $ghnDeliveryRepo, GHNServiceRepository $serviceRepo, EccubeConfig $eccubeConfig)
    {
        parent::__construct($registry, GHNOrder::class);
        $this->session = $session;
        $this->apiService = $apiService;
        $this->warehouseRepo = $warehouseRepo;
        $this->ghnDeliveryRepo = $ghnDeliveryRepo;
        $this->serviceRepo = $serviceRepo;
        $this->eccubeConfig = $eccubeConfig;
    }

    /**
     * @param $shipping
     * @param $service
     * @param $warehouse
     * @return GHNOrder
     */
    public function buildGHNOrder($shipping, $service, $warehouse): GHNOrder
    {
        /** @var GHNOrder $ghnOrder */
        $ghnOrder = $this->findOneBy(['Shipping' => $shipping]);
        if (!$ghnOrder || !$ghnOrder->isCreatedOrder()) {
            $ghnOrder = new GHNOrder();
            $ghnOrder->setShipping($shipping);
        }
        // set new service
        $ghnOrder->setGHNService($service);
        // set new warehouse
        $ghnOrder->setGHNWarehouse($warehouse);

        return $ghnOrder;
    }

    /**
     * @param Order $order
     * @throws \Doctrine\ORM\ORMException
     * @throws \Plugin\GHNDelivery\Service\GHNException
     */
    public function createGHNOrderByOrder(Order $order)
    {
        /** @var Shipping $shipping */
        foreach ($order->getShippings() as $shipping) {
            $this->createGHNOrderByShipping($shipping);
        }
    }

    /**
     * @param Shipping $shipping
     * @param bool $isAutoUpdate
     * @return bool
     * @throws \Doctrine\ORM\ORMException
     * @throws \Plugin\GHNDelivery\Service\GHNException
     */
    public function createGHNOrderByShipping(Shipping $shipping, $isAutoUpdate = true)
    {
        $warehouse = $this->warehouseRepo->getOne();
        $isGHNDelivery = $this->ghnDeliveryRepo->find($shipping->getDelivery());
        $arr = ['%shipping%' => $shipping->getId()];

        if (!$isGHNDelivery) {
            $this->session->getFlashBag()->add('eccube.admin.error', trans('ghn.shipping.not_ghn', $arr));
            return false;
        }

        $service = $shipping->getGHNService();
        if (!$service) {
            $this->session->getFlashBag()->add('eccube.admin.error', trans('ghn.shipping.service.not_found', $arr));

            return false;
        }

        /** @var GHNOrder $ghnOrder */
        $ghnOrder = $this->buildGHNOrder($shipping, $service, $warehouse);
        $output = $this->callOrderApi($ghnOrder, $isAutoUpdate);

        if (!$output->getCode()) {
            $this->session->getFlashBag()->add('eccube.admin.error', $output->getMsg() ? $output->getMsg() : trans('ghn.shipping.cannot_create', $arr));

            return false;
        }

        // save information
        $ghnOrder->setReturnData(serialize($output->getData()));
        $ghnOrder->setStatus(GHNOrderStatus::READY_TO_PICK);
        $this->getEntityManager()->persist($ghnOrder);

        return true;
    }

    /**
     * @param GHNOrder $ghnOrder
     * @param bool $isAutoUpdate
     * @return ApiParserService
     * @throws \Plugin\GHNDelivery\Service\GHNException
     */
    public function callOrderApi(GHNOrder $ghnOrder, $isAutoUpdate = true)
    {
        $output = new ApiParserService();
        // update
        if ($ghnOrder->getId()) {
            if ($isAutoUpdate) {
                $output = $this->apiService->updateGHNOrder($ghnOrder);
            } else {
                $this->session->getFlashBag()->add('eccube.admin.warning', trans('ghn.shipping.create_already', ['%shipping%' => $ghnOrder->getShipping()->getId()]));
            }
        } else {
            // create
            $output = $this->apiService->createGHNOrder($ghnOrder, 803854);
        }

        return $output;
    }

    /**
     * @param Order $order
     * @return bool
     * @throws \Doctrine\ORM\ORMException
     * @throws \Plugin\GHNDelivery\Service\GHNException
     */
    public function cancelGHNOrderByOrder(Order $order)
    {
        foreach ($order->getShippings() as $shipping) {
            $this->cancelGHNOrderByShipping($shipping);
        }
        return true;
    }

    /**
     * @param Shipping $shipping
     * @return bool
     * @throws \Doctrine\ORM\ORMException
     * @throws \Plugin\GHNDelivery\Service\GHNException
     */
    public function cancelGHNOrderByShipping(Shipping $shipping)
    {
        $arr = ['%shipping%' => $shipping->getId()];
        $isGHNDelivery = $this->ghnDeliveryRepo->find($shipping->getDelivery());
        if (!$isGHNDelivery) {
            $this->session->getFlashBag()->add('eccube.admin.error', trans('ghn.shipping.not_ghn', $arr));
            return false;
        }
        /** @var GHNOrder $ghnOrder */
        $ghnOrder = $this->findOneBy(['Shipping' => $shipping]);
        if (!$ghnOrder) {
            $this->session->getFlashBag()->add('eccube.admin.error', trans('ghn.shipping.not_found', $arr));
            return false;
        }
        $output = $this->apiService->cancelOrder($ghnOrder);

        if (!$output->getCode()) {
            $this->session->getFlashBag()->add('eccube.admin.error', $output->getMsg() ? $output->getMsg() : trans('ghn.shipping.cancel.incorrect', $arr));

            return false;
        }

        $ghnOrder->setStatus(GHNOrderStatus::CANCEL);
        $this->getEntityManager()->persist($ghnOrder);
        return true;
    }
}
