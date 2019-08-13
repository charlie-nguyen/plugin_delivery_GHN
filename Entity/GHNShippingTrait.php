<?php
/**
 * Author: lqdung1992@gmail.com
 * Date: 1/28/2019
 * Time: 2:29 PM
 */

namespace Plugin\OSGHNDelivery\Entity;

use Doctrine\ORM\Mapping as ORM;
use Eccube\Annotation as Eccube;

/**
 * Trait GHNShippingTrait
 * @package Plugin\OSGHNDelivery\Entity
 *
 * @Eccube\EntityExtension("Eccube\Entity\Shipping")
 */
trait GHNShippingTrait
{
    use FullAddressTrait;
// Todo continue (ver 2.0 - fee)
//    /**
//     * Allowed values: CHOTHUHANG, CHOXEMHANGKHONGTHU, KHONGCHOXEMHANG
//     *
//     * @var string
//     *
//     * @ORM\Column(name="note_code", type="string", length=255, nullable=true)
//     */
//    private $note_code;
//
//    /**
//     * @var float
//     *
//     * @ORM\Column(name="insurance_fee", type="float", precision=0)
//     */
//    private $insurance_fee = 0;
//
//    /**
//     * @var string
//     *
//     * @ORM\Column(name="note", nullable=true, type="string", length=3000)
//     */
//    private $note;
//
//    /**
//     * @var string
//     *
//     * @ORM\Column(name="coupon_code", type="string", length=30, nullable=true)
//     */
//    private $coupon_code;

    /**
     * @var ?GHNPref
     * @ORM\ManyToOne(targetEntity="Plugin\OSGHNDelivery\Entity\GHNPref", inversedBy="Shippings")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ghn_pref_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     * })
     */
    private $GHNPref;

    /**
     * @var GHNService|null
     *
     * @ORM\OneToOne(targetEntity="Plugin\OSGHNDelivery\Entity\GHNService", mappedBy="Shipping")
     */
    private $GHNService;

    /**
     * @return GHNPref
     */
    public function getGHNPref()
    {
        return $this->GHNPref;
    }

    /**
     * @param $GHNPref
     * @return $this
     */
    public function setGHNPref(GHNPref $GHNPref)
    {
        $this->GHNPref = $GHNPref;

        return $this;
    }

    /**
     * @return null|GHNService
     */
    public function getGHNService(): ?GHNService
    {
        return $this->GHNService;
    }

    /**
     * @param null|GHNService $GHNService
     * @return $this
     */
    public function setGHNService(?GHNService $GHNService)
    {
        $this->GHNService = $GHNService;

        return $this;
    }
}
