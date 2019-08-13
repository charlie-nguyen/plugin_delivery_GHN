<?php
/**
 * Author: lqdung1992@gmail.com
 * Date: 2/21/2019
 * Time: 11:22 AM
 */

namespace Plugin\OSGHNDelivery\Entity;

use Doctrine\ORM\Mapping as ORM;
use Eccube\Annotation as Eccube;

/**
 * Trait DeliveryTrait
 * @package Plugin\OSGHNDelivery\Entity
 *
 * @Eccube\EntityExtension("Eccube\Entity\Delivery")
 */
trait DeliveryTrait
{
    /**
     * @var GHNDelivery
     *
     * @ORM\OneToOne(targetEntity="Plugin\OSGHNDelivery\Entity\GHNDelivery", mappedBy="Delivery")
     */
    private $GHNDelivery;

    /**
     * @return GHNDelivery
     */
    public function getGHNDelivery()
    {
        return $this->GHNDelivery;
    }

    /**
     * @param GHNDelivery $GHNDelivery
     * @return $this
     */
    public function setGHNDelivery(GHNDelivery $GHNDelivery)
    {
        $this->GHNDelivery = $GHNDelivery;
        return $this;
    }

    public function isGHNDelivery()
    {
        return (empty($this->GHNDelivery) ? false : true);
    }
}
