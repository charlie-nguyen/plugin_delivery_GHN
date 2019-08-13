<?php
/**
 * Author: lqdung1992@gmail.com
 * Date: 2/15/2019
 * Time: 4:27 PM
 */

namespace Plugin\OSGHNDelivery\Repository;


use Doctrine\Common\Persistence\ManagerRegistry;
use Eccube\Common\EccubeConfig;
use Eccube\Entity\Shipping;
use Eccube\Repository\AbstractRepository;
use Plugin\OSGHNDelivery\Entity\GHNService;
use Plugin\OSGHNDelivery\Service\ApiService;
use Symfony\Component\HttpFoundation\Session\Session;

class GHNServiceRepository extends AbstractRepository
{
    /** @var GHNConfigRepository */
    protected $configRepo;

    /** @var Session */
    protected $session;

    /** @var ApiService */
    protected $apiService;

    /** @var GHNWarehouseRepository */
    protected $warehouseRepo;

    /**
     * GHNServiceRepository constructor.
     * @param ManagerRegistry $registry
     * @param GHNConfigRepository $configRepo
     * @param Session $session
     * @param ApiService $apiService
     * @param GHNWarehouseRepository $warehouseRepo
     * @param EccubeConfig $eccubeConfig
     */
    public function __construct(ManagerRegistry $registry, GHNConfigRepository $configRepo, Session $session, ApiService $apiService, GHNWarehouseRepository $warehouseRepo, EccubeConfig $eccubeConfig)
    {
        parent::__construct($registry, GHNService::class);
        $this->configRepo = $configRepo;
        $this->session = $session;
        $this->apiService = $apiService;
        $this->warehouseRepo = $warehouseRepo;
        $this->setEccubeConfig($eccubeConfig);
    }


    /**
     * @param $shp
     * @param $mainServiceId
     * @param $warehouse
     * @return GHNService
     */
    public function buildGHNService(Shipping $shp, $mainServiceId): GHNService
    {
        $warehouse = $this->warehouseRepo->getOne();
        /** @var GHNService $service */
        $service = $this->findOneBy(['Shipping' => $shp]);
        if (!$service) {
            $service = new GHNService();
            $service->setShipping($shp);
        }
        $service->setMainServiceId($mainServiceId);

        // get service ID + fee
        $fromGHNPref = $warehouse->getGHNPref();
        $service->setFromPref($fromGHNPref);

        $toGHNPref = $shp->getGHNPref();
        $service->setToPref($toGHNPref);
        $service->setWeight($this->configRepo->find(1)->getWeight());

        return $service;
    }

    /**
     * @param Shipping $shipping
     * @param $mainServiceId
     * @param null $index
     * @return bool
     * @throws \Doctrine\ORM\ORMException
     */
    public function createGHNServiceByShipping(Shipping $shipping, $mainServiceId, $index = null)
    {
        $service = $this->buildGHNService($shipping, $mainServiceId);
        if (!is_null($index)) {
            $sessionKey = $this->eccubeConfig->get('admin_ghn_session_service_fee').$index;
        } else {
            $sessionKey = $this->eccubeConfig->get('admin_ghn_session_service_fee');
        }

        $data = $this->session->get($sessionKey, []);
        $arrMsg = ['%shipping%' => $shipping->getId()];
        if (empty($data)) {
            $output = $this->apiService->findAvailableServices($service->getFromDistrictId(), $service->getToDistrictId());
            // call api to get service list
            $data = $output->getData();
            // error
            if (empty($data) || !is_array($data)) {
                $this->session->getFlashBag()->add('eccube.admin.error', trans('ghn.shipping.service.cannot_connect', $arrMsg));

                return false;
            }
        }

        if (!$service->setMainService($data)) {
            $this->session->getFlashBag()->add('eccube.admin.error', trans('ghn.shipping.service.incorrect', $arrMsg));

            return false;
        }
        $shipping->setGHNService($service);
        $this->getEntityManager()->persist($service);
        return true;
    }
}
