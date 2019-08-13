<?php
/**
 * Author: lqdung1992@gmail.com
 * Date: 2/28/2019
 * Time: 2:26 PM
 */

namespace Plugin\OSGHNDelivery\Repository;


use Doctrine\Common\Persistence\ManagerRegistry;
use Eccube\Repository\AbstractRepository;
use Plugin\OSGHNDelivery\Entity\GHNOrder;
use Plugin\OSGHNDelivery\Entity\GHNOrderCallback;

/**
 * Class GHNOrderCallbackRepository
 * @package Plugin\OSGHNDelivery\Repository
 */
class GHNOrderCallbackRepository extends AbstractRepository
{
    /** @var GHNOrderRepository */
    private $ghnOrderRepo;
    /**
     * GHNOrderCallbackRepository constructor.
     * @param ManagerRegistry $registry
     * @param string $entityClass
     */
    public function __construct(ManagerRegistry $registry, GHNOrderRepository $ghnOrderRepo)
    {
        parent::__construct($registry, GHNOrderCallback::class);
        $this->ghnOrderRepo = $ghnOrderRepo;
    }

    /**
     * @param $data
     * @param array $header
     * @return bool
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function saveCallbackData($data, $header = [])
    {
        if (is_string($data)) {
            $data = json_decode($data, true);
        }

        $Callback = new GHNOrderCallback();
        $Callback->setCallbackData(serialize($data));

        // status callback
        if (isset($data['CurrentStatus']) && isset($data['OrderCode'])) {
            $Callback->setType(GHNOrderCallback::STATUS_CALLBACK);
            /** @var GHNOrder $ghnOrder */
            $ghnOrder = $this->ghnOrderRepo->findOneBy(['OrderCode' => $data['OrderCode']]);
            if ($ghnOrder) {
                $ghnOrder->setStatus($data['CurrentStatus']);
                $ghnOrder->addGHNOrderCallbacks($Callback);
                $Callback->setGHNOrder($ghnOrder);
                $this->getEntityManager()->persist($ghnOrder);
            }
        } else {
            // cod callback
            $Callback->setType(GHNOrderCallback::COD_CALLBACK);
        }

        $Callback->setHeader(serialize($header));

        $this->getEntityManager()->persist($Callback);
        $this->getEntityManager()->flush();


        return true;
    }
}
