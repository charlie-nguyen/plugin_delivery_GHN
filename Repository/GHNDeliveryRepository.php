<?php
/**
 * Author: lqdung1992@gmail.com
 * Date: 1/30/2019
 * Time: 2:29 PM
 */

namespace Plugin\OSGHNDelivery\Repository;


use Doctrine\Common\Persistence\ManagerRegistry;
use Eccube\Repository\AbstractRepository;
use Plugin\GHNDelivery\Entity\GHNDelivery;

/**
 * Class GHNDeliveryRepository
 * @package Plugin\OSGHNDelivery\Repository
 */
class GHNDeliveryRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry, string $entityClass = GHNDelivery::class)
    {
        parent::__construct($registry, $entityClass);
    }
}