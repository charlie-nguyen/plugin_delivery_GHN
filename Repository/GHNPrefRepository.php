<?php

namespace Plugin\OSGHNDelivery\Repository;

use Eccube\Repository\AbstractRepository;
use Plugin\OSGHNDelivery\Entity\GHNPref;

/**
 * Class GHNPrefRepository
 * @package Plugin\OSGHNDelivery\Repository
 */
class GHNPrefRepository extends AbstractRepository
{
    public function __construct(\Doctrine\Common\Persistence\ManagerRegistry $registry, string $entityClass = GHNPref::class)
    {
        parent::__construct($registry, $entityClass);
    }
}
