<?php
/**
 * Author: lqdung1992@gmail.com
 * Date: 2/20/2019
 * Time: 1:48 PM
 */

namespace Plugin\OSGHNDelivery\Entity;

use Doctrine\ORM\Mapping as ORM;
use Eccube\Annotation as Eccube;

/**
 * Trait GHNOrderTrait
 * @package Plugin\OSGHNDelivery\Entity
 *
 * @Eccube\EntityExtension("Eccube\Entity\Order")
 */
trait GHNOrderTrait
{
    /**
     * @var bool
     *
     * @ORM\Column(name="is_ghn_cod", type="boolean", options={"default":false}, nullable=true)
     */
    private $isGHNCOD = false;

    /**
     * @return bool
     */
    public function isGHNCOD()
    {
        return $this->isGHNCOD;
    }

    /**
     * @param bool $isGHNCOD
     */
    public function setIsGHNCOD(bool $isGHNCOD)
    {
        $this->isGHNCOD = $isGHNCOD;

        return $this;
    }
}