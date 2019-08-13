<?php
/**
 * Author: lqdung1992@gmail.com
 * Date: 1/30/2019
 * Time: 2:27 PM
 */

namespace Plugin\OSGHNDelivery\Entity;


use Eccube\Entity\AbstractEntity;
use Doctrine\ORM\Mapping as ORM;
use Eccube\Entity\Delivery;

/**
 * Class GHNDelivery
 * @package Plugin\OSGHNDelivery\Entity
 *
 * @ORM\Table(name="plg_ghn_delivery")
 * @ORM\Entity(repositoryClass="Plugin\OSGHNDelivery\Repository\GHNDeliveryRepository")
 */
class GHNDelivery extends AbstractEntity
{
    /**
     * @var Delivery
     *
     * @ORM\OneToOne(targetEntity="Eccube\Entity\Delivery", inversedBy="OSGHNDelivery")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id", referencedColumnName="id")
     * })
     * @ORM\Id
     */
    private $Delivery;

    /**
     * @return Delivery
     */
    public function getDelivery()
    {
        return $this->Delivery;
    }

    /**
     * @param Delivery $Delivery
     */
    public function setDelivery(Delivery $Delivery)
    {
        $this->Delivery = $Delivery;
    }
}
