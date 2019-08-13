<?php
/**
 * Author: lqdung1992@gmail.com
 * Date: 2/15/2019
 * Time: 4:26 PM
 */

namespace Plugin\OSGHNDelivery\Entity;

use Doctrine\ORM\Mapping as ORM;
use Eccube\Entity\AbstractEntity;
use Eccube\Entity\OrderItem;
use Eccube\Entity\Shipping;


/**
 * User service get from api
 *
 * @ORM\Table(name="plg_ghn_delivery_service")
 * @ORM\Entity(repositoryClass="Plugin\OSGHNDelivery\Repository\GHNServiceRepository")
 */
class GHNService extends AbstractEntity
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", options={"unsigned":true})
     * @ORM\Id
     * @ORM\GeneratedValue()
     */
    private $id;

    /**
     * @var Shipping
     *
     * @ORM\OneToOne(targetEntity="Eccube\Entity\Shipping", inversedBy="GHNService")
     * @ORM\JoinColumns(
     *     @ORM\JoinColumn(name="shipping_id", referencedColumnName="id", onDelete="CASCADE")
     * )
     */
    private $Shipping;

    /**
     * @var int
     *
     * @ORM\Column(name="from_district_id", type="integer", nullable=false)
     */
    private $from_district_id;

    /**
     * @var string
     *
     * @ORM\Column(name="from_district_name", type="string", length=255, nullable=false)
     */
    private $from_district_name;

    /**
     * @var int
     *
     * @ORM\Column(name="from_ghn_pref_id", type="integer", nullable=false)
     */
    private $from_ghn_pref_id;

    /**
     * @var int
     *
     * @ORM\Column(name="to_district_id", type="integer", nullable=false)
     */
    private $to_district_id;

    /**
     * @var string
     *
     * @ORM\Column(name="to_district_name", type="string", length=255, nullable=false)
     */
    private $to_district_name;

    /**
     * @var int
     *
     * @ORM\Column(name="to_ghn_pref_id", type="integer", nullable=false)
     */
    private $to_ghn_pref_id;

    /**
     * @var float
     *
     * @ORM\Column(name="weight", type="float", precision=1, nullable=false)
     */
    private $weight;

    /**
     * @var int
     *
     * @ORM\Column(name="main_service_id", type="integer", nullable=false)
     */
    private $main_service_id;

    /**
     * @var string
     *
     * @ORM\Column(name="main_service_name", type="string", length=255, nullable=false)
     */
    private $main_service_name;

    /**
     * @var float
     *
     * @ORM\Column(name="main_service_fee", type="float", precision=2, nullable=false)
     */
    private $main_service_fee;

    /**
     * json extra services
     *
     * @var string
     *
     * @ORM\Column(name="extra_services", type="text", nullable=true)
     */
    private $extra_services;

    /**
     * @var OrderItem
     *
     * @ORM\OneToOne(targetEntity="Eccube\Entity\OrderItem")
     * @ORM\JoinColumns(
     *     @ORM\JoinColumn(name="order_item_id", referencedColumnName="id", onDelete="CASCADE")
     * )
     */
    private $OrderItem;

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
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return GHNService
     */
    public function setId(int $id): GHNService
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return Shipping
     */
    public function getShipping()
    {
        return $this->Shipping;
    }

    /**
     * @param Shipping $Shipping
     * @return GHNService
     */
    public function setShipping(Shipping $Shipping)
    {
        $this->Shipping = $Shipping;
        return $this;
    }

    /**
     * @return int
     */
    public function getFromDistrictId()
    {
        return $this->from_district_id;
    }

    /**
     * @param int $from_district_id
     * @return GHNService
     */
    public function setFromDistrictId(int $from_district_id)
    {
        $this->from_district_id = $from_district_id;
        return $this;
    }

    /**
     * @return string
     */
    public function getFromDistrictName()
    {
        return $this->from_district_name;
    }

    /**
     * @param string $from_district_name
     * @return GHNService
     */
    public function setFromDistrictName(string $from_district_name)
    {
        $this->from_district_name = $from_district_name;
        return $this;
    }

    /**
     * @return int
     */
    public function getToDistrictId()
    {
        return $this->to_district_id;
    }

    /**
     * @param int $to_district_id
     * @return GHNService
     */
    public function setToDistrictId(int $to_district_id)
    {
        $this->to_district_id = $to_district_id;
        return $this;
    }

    /**
     * @return string
     */
    public function getToDistrictName()
    {
        return $this->to_district_name;
    }

    /**
     * @param string $to_district_name
     * @return GHNService
     */
    public function setToDistrictName(string $to_district_name): GHNService
    {
        $this->to_district_name = $to_district_name;
        return $this;
    }

    /**
     * @return float
     */
    public function getWeight()
    {
        return $this->weight;
    }

    /**
     * @param float $weight
     * @return GHNService
     */
    public function setWeight(float $weight): GHNService
    {
        $this->weight = $weight;
        return $this;
    }

    /**
     * @return int
     */
    public function getMainServiceId()
    {
        return $this->main_service_id;
    }

    /**
     * @param int $main_service_id
     * @return GHNService
     */
    public function setMainServiceId(int $main_service_id): GHNService
    {
        $this->main_service_id = $main_service_id;
        return $this;
    }

    /**
     * @return string
     */
    public function getMainServiceName()
    {
        return $this->main_service_name;
    }

    /**
     * @param string $main_service_name
     * @return GHNService
     */
    public function setMainServiceName(string $main_service_name): GHNService
    {
        $this->main_service_name = $main_service_name;
        return $this;
    }

    /**
     * @return float
     */
    public function getMainServiceFee()
    {
        return $this->main_service_fee;
    }

    /**
     * @param float $main_service_fee
     * @return GHNService
     */
    public function setMainServiceFee(float $main_service_fee): GHNService
    {
        $this->main_service_fee = $main_service_fee;
        return $this;
    }

    /**
     * @return string
     */
    public function getExtraServices()
    {
        return $this->extra_services;
    }

    /**
     * @param string $extra_services
     * @return GHNService
     */
    public function setExtraServices(string $extra_services): GHNService
    {
        $this->extra_services = $extra_services;
        return $this;
    }

    /**
     * @return int
     */
    public function getFromGhnPrefId()
    {
        return $this->from_ghn_pref_id;
    }

    /**
     * @param int $from_ghn_pref_id
     * @return GHNService
     */
    public function setFromGhnPrefId(int $from_ghn_pref_id): GHNService
    {
        $this->from_ghn_pref_id = $from_ghn_pref_id;
        return $this;
    }

    /**
     * @return int
     */
    public function getToGhnPrefId()
    {
        return $this->to_ghn_pref_id;
    }

    /**
     * @param int $to_ghn_pref_id
     * @return GHNService
     */
    public function setToGhnPrefId(int $to_ghn_pref_id): GHNService
    {
        $this->to_ghn_pref_id = $to_ghn_pref_id;
        return $this;
    }

    public function setFromPref(GHNPref $GHNPref)
    {
        $this->setFromDistrictId((int) $GHNPref->getDistrictId());
        $this->setFromGhnPrefId((int)$GHNPref->getId());
        $this->setFromDistrictName($GHNPref->getDistrictName());
    }

    public function setToPref(GHNPref $GHNPref)
    {
        $this->setToDistrictId((int) $GHNPref->getDistrictId());
        $this->setToGhnPrefId((int)$GHNPref->getId());
        $this->setToDistrictName($GHNPref->getDistrictName());
    }

    public function setMainService(array $data)
    {
        foreach ($data as $item) {
            if (isset($item['ServiceID']) && $item['ServiceID'] == $this->getMainServiceId()) {
                $this->setMainServiceFee($item['ServiceFee'])
                    ->setMainServiceName($item['Name']);
                return true;
                // todo: extra services
            }
        }

        return false;
    }

    /**
     * @return OrderItem
     */
    public function getOrderItem()
    {
        return $this->OrderItem;
    }

    /**
     * @param OrderItem $orderItem
     * @return GHNService
     */
    public function setOrderItem(OrderItem $orderItem)
    {
        $this->OrderItem = $orderItem;

        return $this;
    }
}
