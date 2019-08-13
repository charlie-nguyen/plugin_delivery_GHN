<?php
/**
 * Author: lqdung1992@gmail.com
 * Date: 1/29/2019
 * Time: 3:31 PM
 */

namespace Plugin\OSGHNDelivery\Entity;


use Doctrine\Common\Collections\ArrayCollection;
use Eccube\Entity\AbstractEntity;
use Doctrine\ORM\Mapping as ORM;
use Eccube\Entity\Master\Pref;
use Eccube\Entity\Shipping;

/**
 * Class GHNPref
 * @package Plugin\OSGHNDelivery\Entity
 * @ORM\Table(name="plg_ghn_pref")
 * @ORM\Entity(repositoryClass="Plugin\OSGHNDelivery\Repository\GHNPrefRepository")
 */
class GHNPref extends AbstractEntity
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", options={"unsigned":true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="province_name", type="string", length=255)
     */
    private $province_name;

    /**
     * @var string
     *
     * @ORM\Column(name="province_id", type="integer")
     */
    private $province_id;

    /**
     * @var string
     *
     * @ORM\Column(name="district_name", type="string", length=255)
     */
    private $district_name;

    /**
     * @var string
     *
     * @ORM\Column(name="district_code", type="string", length=255)
     */
    private $district_code;

    /**
     * @var string
     *
     * @ORM\Column(name="district_id", type="integer")
     */
    private $district_id;

    /**
     * @var Pref
     *
     * @ORM\ManyToOne(targetEntity="Eccube\Entity\Master\Pref")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="pref_eccube_id", referencedColumnName="id")
     * })
     */
    private $Pref;

    /**
     * @var Shipping[]
     * @ORM\OneToMany(targetEntity="Eccube\Entity\Shipping", mappedBy="GHNPref")
     */
    private $Shippings;

    /**
     * @var GHNWarehouse[]
     *
     * @ORM\OneToMany(targetEntity="Plugin\OSGHNDelivery\Entity\GHNWarehouse", mappedBy="GHNPref")
     */
    private $Warehouses;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="create_date", type="datetimetz", nullable=true)
     */
    private $create_date;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="update_date", type="datetimetz", nullable=true)
     */
    private $update_date;

    /**
     * @return \DateTime
     */
    public function getCreateDate()
    {
        return $this->create_date;
    }

    /**
     * @param \DateTime $create_date
     * @return $this
     */
    public function setCreateDate(\DateTime $create_date)
    {
        $this->create_date = $create_date;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getUpdateDate()
    {
        return $this->update_date;
    }

    /**
     * @param \DateTime $update_date
     * @return $this
     */
    public function setUpdateDate(\DateTime $update_date)
    {
        $this->update_date = $update_date;
        return $this;
    }

    /**
     * GHNPref constructor.
     */
    public function __construct()
    {
        $this->Warehouses = new ArrayCollection();
        $this->Shippings = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getProvinceName()
    {
        return $this->province_name;
    }

    /**
     * @param string $province_name
     */
    public function setProvinceName(string $province_name): void
    {
        $this->province_name = $province_name;
    }

    /**
     * @return string
     */
    public function getProvinceId()
    {
        return $this->province_id;
    }

    /**
     * @param string $province_id
     */
    public function setProvinceId(string $province_id): void
    {
        $this->province_id = $province_id;
    }

    /**
     * @return string
     */
    public function getDistrictName()
    {
        return $this->district_name;
    }

    /**
     * @param string $district_name
     */
    public function setDistrictName(string $district_name): void
    {
        $this->district_name = $district_name;
    }

    /**
     * @return string
     */
    public function getDistrictCode()
    {
        return $this->district_code;
    }

    /**
     * @param string $district_code
     */
    public function setDistrictCode(string $district_code): void
    {
        $this->district_code = $district_code;
    }

    /**
     * @return int
     */
    public function getDistrictId()
    {
        return (int) $this->district_id;
    }

    /**
     * @param $district_id
     */
    public function setDistrictId($district_id): void
    {
        $this->district_id = $district_id;
    }

    /**
     * @return Pref
     */
    public function getPref()
    {
        return $this->Pref;
    }

    /**
     * @param Pref $Pref
     */
    public function setPref($Pref): void
    {
        $this->Pref = $Pref;
    }

    /**
     * @return GHNWarehouse[]
     */
    public function getWarehouses()
    {
        return $this->Warehouses;
    }

    /**
     * @param GHNWarehouse $Warehouse
     */
    public function addWarehouse(GHNWarehouse $Warehouse)
    {
        if (is_null($this->Warehouses)) {
            $this->Warehouses = new ArrayCollection();
        }

        $this->Warehouses->add($Warehouse);
    }

    /**
     * @param GHNWarehouse $Warehouse
     */
    public function removeWarehouse($Warehouse)
    {
        $this->Warehouses->removeElement($Warehouse);
    }

    /**
     * @return Shipping[]
     */
    public function getShippings()
    {
        return $this->Shippings;
    }

    /**
     * @param Shipping $shp
     */
    public function addShipping(Shipping $shp)
    {
        if (is_null($this->Shippings)) {
            $this->Shippings = new ArrayCollection();
        }

        $this->Shippings->add($shp);
    }

    /**
     * @param GHNWarehouse $Warehouse
     */
    public function removeShipping(Shipping $shp)
    {
        $this->Shippings->removeElement($shp);
    }
}
