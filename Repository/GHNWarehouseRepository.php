<?php
/**
 * Author: lqdung1992@gmail.com
 * Date: 2/14/2019
 * Time: 5:07 PM
 */

namespace Plugin\OSGHNDelivery\Repository;


use Doctrine\Common\Persistence\ManagerRegistry;
use Eccube\Entity\BaseInfo;
use Eccube\Repository\AbstractRepository;
use Eccube\Repository\BaseInfoRepository;
use Plugin\OSGHNDelivery\Entity\GHNWarehouse;
use Plugin\OSGHNDelivery\Service\ApiService;

class GHNWarehouseRepository extends AbstractRepository
{
    /**
     * @var BaseInfo
     */
    private $baseInfo;

    /** @var ApiService */
    protected $apiService;

    /** @var GHNPrefRepository */
    protected $GHNPrefRepo;

    /**
     * GHNWarehouseRepository constructor.
     * @param ManagerRegistry $registry
     * @param BaseInfoRepository $baseInfoRepository
     * @param ApiService $apiService
     * @param GHNPrefRepository $GHNPrefRepo
     * @throws \Exception
     */
    public function __construct(ManagerRegistry $registry, BaseInfoRepository $baseInfoRepository, ApiService $apiService, GHNPrefRepository $GHNPrefRepo)
    {
        $this->baseInfo = $baseInfoRepository->get();
        $this->apiService = $apiService;
        $this->GHNPrefRepo = $GHNPrefRepo;
        parent::__construct($registry, GHNWarehouse::class);
    }

    /**
     * @param bool $isAutoSave
     * @return null|object|GHNWarehouse
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function getOrCreate($isAutoSave = false)
    {
        $Warehouse = $this->findOneBy([]);

        if (is_null($Warehouse)) {
            $Warehouse = new GHNWarehouse();
            $Warehouse->setEmail($this->baseInfo->getEmail02());

            // get warehouse list
            $output = $this->apiService->getWarehouse();
            if ($output->getCode()) {
                $isCanSave = false;
                foreach ($output->getData() as $warehouse) {
                    if (isset($warehouse['IsMain']) && $warehouse['IsMain']) {
                        $isCanSave = true;
                        $pref = $this->GHNPrefRepo->findOneBy(['district_id' => $warehouse['DistrictID']]);
                        $Warehouse->setWarehouseFromApiData($warehouse, $pref);
                        break;
                    }
                }

                if ($isAutoSave && $isCanSave) {
                    $this->getEntityManager()->persist($Warehouse);
                    $this->getEntityManager()->flush($Warehouse);
                }
            }
        }

        return $Warehouse;
    }

    /**
     * @return GHNWarehouse
     */
    public function getOne()
    {
        return $this->findOneBy([]);
    }


}