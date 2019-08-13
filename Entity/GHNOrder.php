<?php
/**
 * Author: lqdung1992@gmail.com
 * Date: 2/20/2019
 * Time: 11:47 AM
 */

namespace Plugin\OSGHNDelivery\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Eccube\Entity\AbstractEntity;
use Eccube\Entity\Order;
use Eccube\Entity\Shipping;

/**
 * Class GHNOrder
 * @package Plugin\OSGHNDelivery\Entity
 *
 * @ORM\Table(name="plg_ghn_order")
 * @ORM\Entity(repositoryClass="Plugin\OSGHNDelivery\Repository\GHNOrderRepository")
 */
class GHNOrder extends AbstractEntity
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", options={"unsigned":true})
     * @ORM\Id
     * @ORM\GeneratedValue()
     */
    private $id;

    /**
     * @var Order
     *
     * @ORM\ManyToOne(targetEntity="Eccube\Entity\Order")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="order_id", referencedColumnName="id", onDelete="SET NULL")
     * })
     */
    private $Order;

    /**
     * @var Shipping
     *
     * @ORM\OneToOne(targetEntity="Eccube\Entity\Shipping")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="shipping_id", referencedColumnName="id", onDelete="SET NULL")
     * })
     */
    private $Shipping;

    /**
     * @var GHNService
     *
     * @ORM\OneToOne(targetEntity="Plugin\OSGHNDelivery\Entity\GHNService")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="service_id", referencedColumnName="id", onDelete="SET NULL")
     * })
     */
    private $GHNService;

    /**
     * @var GHNWarehouse
     *
     * @ORM\ManyToOne(targetEntity="Plugin\OSGHNDelivery\Entity\GHNWarehouse")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="warehouse_id", referencedColumnName="id", onDelete="SET NULL")
     * })
     */
    private $GHNWarehouse;

    /**
     * @var string
     *
     * @ORM\Column(name="token", type="string", length=255)
     */
    private $token;

    /**
     * Choose who pay shipping fee.
     * 1: Shop/Seller.
     * 2: Buyer/Consignee.
     * 4: GHN Wallet.
     * 5: Credit.
     * Default value: 1 or depend on each specific account
     * Allowed values: 1, 2
     *
     * @var int
     *
     * @ORM\Column(name="payment_type", type="smallint")
     */
    private $PaymentTypeID;

    /**
     * @var int
     *
     * @ORM\Column(name="from_district_id", type="integer")
     */
    private $FromDistrictID;

    /**
     * @var string
     *
     * @ORM\Column(name="from_ward_code", type="string", length=30, nullable=true)
     */
    private $FromWardCode;

    /**
     * @var int
     *
     * @ORM\Column(name="to_district_id", type="integer")
     */
    private $ToDistrictID;

    /**
     * @var string
     *
     * @ORM\Column(name="to_ward_code", type="string", length=30, nullable=true)
     */
    private $ToWardCode;

    /**
     * @var string
     *
     * @ORM\Column(name="note", type="text", nullable=true)
     */
    private $Note;

    /**
     * @var string
     *
     * @ORM\Column(name="seal_code", type="string", length=255, nullable=true)
     */
    private $SealCode;

    /**
     * @var string
     *
     * @ORM\Column(name="external_code", type="string", length=255, unique=true, nullable=true)
     */
    private $ExternalCode;

    /**
     * @var string
     *
     *@ORM\Column(name="client_contact_name", type="string", length=255)
     */
    private $ClientContactName;

    /**
     * @var string
     *
     * @ORM\Column(name="client_contact_phone", type="string", length=20)
     */
    private $ClientContactPhone;

    /**
     * @var string
     *
     * @ORM\Column(name="client_address", type="string", length=255)
     */
    private $ClientAddress;

    /**
     * @var string
     *
     * @ORM\Column(name="customer_name", type="string", length=255)
     */
    private $CustomerName;

    /**
     * @var string
     *
     * @ORM\Column(name="customer_phone", type="string", length=20)
     */
    private $CustomerPhone;

    /**
     * @var string
     *
     * @ORM\Column(name="shipping_address", type="string", length=255)
     */
    private $ShippingAddress;

    /**
     * @var float
     *
     * @ORM\Column(name="cod_amount", type="float", precision=0, nullable=true)
     */
    private $CoDAmount = 0;

    /**
     * @var string
     *
     * @ORM\Column(name="note_code", type="string")
     */
    private $NoteCode;

    /**
     * @var float
     *
     * @ORM\Column(name="insurance_fee", type="float", precision=0)
     */
    private $InsuranceFee = 0;

    /**
     * @var int
     *
     * @ORM\Column(name="client_hub_id", type="integer")
     */
    private $ClientHubID;

    /**
     * @var int
     *
     * @ORM\Column(name="main_service_id", type="integer")
     */
    private $ServiceID;

    /**
     * @var string
     *
     * @ORM\Column(name="to_latitude", type="string", nullable=true)
     */
    private $ToLatitude;

    /**
     * @var string
     *
     * @ORM\Column(name="to_longitude", type="string", nullable=true)
     */
    private $ToLongitude;

    /**
     * @var string
     *
     * @ORM\Column(name="from_lat", type="string", nullable=true)
     */
    private $FromLat;

    /**
     * @var string
     *
     * @ORM\Column(name="from_lng", type="string", nullable=true)
     */
    private $FromLng;

    /**
     * @var string
     *
     * @ORM\Column(name="content", type="text", nullable=true)
     */
    private $Content;

    /**
     * @var string
     *
     * @ORM\Column(name="coupon_code", type="string", nullable=true)
     */
    private $CouponCode;

    /**
     * @var float
     *
     * @ORM\Column(name="weight", type="float", precision=1)
     */
    private $Weight;

    /**
     * @var float
     *
     * @ORM\Column(name="length", type="float", precision=1)
     */
    private $Length;

    /**
     * @var float
     *
     * @ORM\Column(name="width", type="float", precision=1)
     */
    private $Width;

    /**
     * @var float
     *
     * @ORM\Column(name="height", type="float", precision=1)
     */
    private $Height;

    /**
     * @var boolean
     *
     * @ORM\Column(name="check_main_bank_account", type="boolean", nullable=true)
     */
    private $CheckMainBankAccount = false;

    /**
     * @var string
     *
     * @ORM\Column(name="shipping_order_costs", type="text", nullable=true)
     */
    private $ShippingOrderCosts;

    /**
     * @var string
     *
     * @ORM\Column(name="return_contact_name", type="string")
     */
    private $ReturnContactName;

    /**
     * @var string
     *
     * @ORM\Column(name="return_contact_phone", type="string", length=20)
     */
    private $ReturnContactPhone;

    /**
     * @var string
     *
     * @ORM\Column(name="return_address", type="string")
     */
    private $ReturnAddress;

    /**
     * @var int
     *
     * @ORM\Column(name="return_district_id", type="integer")
     */
    private $ReturnDistrictID;
    /**
     * @var string
     *
     * @ORM\Column(name="external_return_code", type="string", length=100)
     */
    private $ExternalReturnCode;
    /**
     * @var boolean
     *
     * @ORM\Column(name="is_credit_create", type="boolean", nullable=true)
     */
    private $IsCreditCreate = false;
    /**
     * @var int
     *
     * @ORM\Column(name="affiliate_id", type="integer")
     */
    private $AffiliateID;

    /**
     * @var string
     *
     * @ORM\Column(name="return_data", type="text", nullable=true)
     */
    private $returnData;

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
     * @var GHNOrderCallback[]
     * @ORM\OneToMany(targetEntity="Plugin\OSGHNDelivery\Entity\GHNOrderCallback", mappedBy="GHNOrder")
     */
    private $GHNOrderCallbacks;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", length=30, nullable=true)
     */
    private $status;

    /**
     * @var string
     *
     * @ORM\Column(name="order_code", type="string", length=30, nullable=true)
     */
    private $OrderCode;

    /**
     * GHNOrder constructor.
     * @param int $id
     */
    public function __construct()
    {
        $this->GHNOrderCallbacks = new ArrayCollection();
    }

    /**
     * @return Order
     */
    public function getOrder()
    {
        return $this->Order;
    }

    /**
     * @param Order $Order
     */
    public function setOrder(Order $Order)
    {
        $this->Order = $Order;
    }

    /**
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @param string $token
     * @return GHNOrder
     */
    public function setToken(string $token)
    {
        $this->token = $token;
        return $this;
    }

    /**
     * @return int
     */
    public function getPaymentTypeID()
    {
        return $this->PaymentTypeID;
    }

    /**
     * @param int $PaymentTypeID
     * @return GHNOrder
     */
    public function setPaymentTypeID(int $PaymentTypeID)
    {
        $this->PaymentTypeID = $PaymentTypeID;
        return $this;
    }

    /**
     * @return int
     */
    public function getFromDistrictID()
    {
        return $this->FromDistrictID;
    }

    /**
     * @param int $FromDistrictID
     * @return GHNOrder
     */
    public function setFromDistrictID(int $FromDistrictID)
    {
        $this->FromDistrictID = $FromDistrictID;
        return $this;
    }

    /**
     * @return string
     */
    public function getFromWardCode()
    {
        return $this->FromWardCode;
    }

    /**
     * @param string $FromWardCode
     * @return GHNOrder
     */
    public function setFromWardCode(string $FromWardCode)
    {
        $this->FromWardCode = $FromWardCode;
        return $this;
    }

    /**
     * @return int
     */
    public function getToDistrictID()
    {
        return $this->ToDistrictID;
    }

    /**
     * @param int $ToDistrictID
     * @return GHNOrder
     */
    public function setToDistrictID(int $ToDistrictID)
    {
        $this->ToDistrictID = $ToDistrictID;
        return $this;
    }

    /**
     * @return string
     */
    public function getToWardCode()
    {
        return $this->ToWardCode;
    }

    /**
     * @param string $ToWardCode
     * @return GHNOrder
     */
    public function setToWardCode(string $ToWardCode)
    {
        $this->ToWardCode = $ToWardCode;
        return $this;
    }

    /**
     * @return string
     */
    public function getNote()
    {
        return $this->Note;
    }

    /**
     * @param string $Note
     * @return GHNOrder
     */
    public function setNote(string $Note)
    {
        $this->Note = $Note;
        return $this;
    }

    /**
     * @return string
     */
    public function getSealCode()
    {
        return $this->SealCode;
    }

    /**
     * @param string $SealCode
     * @return GHNOrder
     */
    public function setSealCode(string $SealCode)
    {
        $this->SealCode = $SealCode;
        return $this;
    }

    /**
     * @return string
     */
    public function getExternalCode()
    {
        return $this->ExternalCode;
    }

    /**
     * @param string $ExternalCode
     * @return GHNOrder
     */
    public function setExternalCode(string $ExternalCode)
    {
        $this->ExternalCode = $ExternalCode;
        return $this;
    }

    /**
     * @return string
     */
    public function getClientContactName()
    {
        return $this->ClientContactName;
    }

    /**
     * @param string $ClientContactName
     * @return GHNOrder
     */
    public function setClientContactName(string $ClientContactName)
    {
        $this->ClientContactName = $ClientContactName;
        return $this;
    }

    /**
     * @return string
     */
    public function getClientContactPhone()
    {
        return $this->ClientContactPhone;
    }

    /**
     * @param string $ClientContactPhone
     * @return GHNOrder
     */
    public function setClientContactPhone(string $ClientContactPhone)
    {
        $this->ClientContactPhone = $ClientContactPhone;
        return $this;
    }

    /**
     * @return string
     */
    public function getClientAddress()
    {
        return $this->ClientAddress;
    }

    /**
     * @param string $ClientAddress
     * @return GHNOrder
     */
    public function setClientAddress(string $ClientAddress)
    {
        $this->ClientAddress = $ClientAddress;
        return $this;
    }

    /**
     * @return string
     */
    public function getCustomerName()
    {
        return $this->CustomerName;
    }

    /**
     * @param string $CustomerName
     * @return GHNOrder
     */
    public function setCustomerName(string $CustomerName)
    {
        $this->CustomerName = $CustomerName;
        return $this;
    }

    /**
     * @return string
     */
    public function getCustomerPhone()
    {
        return $this->CustomerPhone;
    }

    /**
     * @param string $CustomerPhone
     * @return GHNOrder
     */
    public function setCustomerPhone(string $CustomerPhone)
    {
        $this->CustomerPhone = $CustomerPhone;
        return $this;
    }

    /**
     * @return string
     */
    public function getShippingAddress()
    {
        return $this->ShippingAddress;
    }

    /**
     * @param string $ShippingAddress
     * @return GHNOrder
     */
    public function setShippingAddress(string $ShippingAddress)
    {
        $this->ShippingAddress = $ShippingAddress;
        return $this;
    }

    /**
     * @return float
     */
    public function getCoDAmount()
    {
        return $this->CoDAmount;
    }

    /**
     * @param float $CoDAmount
     * @return GHNOrder
     */
    public function setCoDAmount(float $CoDAmount)
    {
        $this->CoDAmount = $CoDAmount;
        return $this;
    }

    /**
     * @return string
     */
    public function getNoteCode()
    {
        return $this->NoteCode;
    }

    /**
     * @param string $NoteCode
     * @return GHNOrder
     */
    public function setNoteCode(string $NoteCode)
    {
        $this->NoteCode = $NoteCode;
        return $this;
    }

    /**
     * @return float
     */
    public function getInsuranceFee()
    {
        return $this->InsuranceFee;
    }

    /**
     * @param float $InsuranceFee
     * @return GHNOrder
     */
    public function setInsuranceFee(float $InsuranceFee)
    {
        $this->InsuranceFee = $InsuranceFee;
        return $this;
    }

    /**
     * @return int
     */
    public function getClientHubID()
    {
        return $this->ClientHubID;
    }

    /**
     * @param int $ClientHubID
     * @return GHNOrder
     */
    public function setClientHubID(int $ClientHubID)
    {
        $this->ClientHubID = $ClientHubID;
        return $this;
    }

    /**
     * @return int
     */
    public function getServiceID()
    {
        return $this->ServiceID;
    }

    /**
     * @param int $ServiceID
     * @return GHNOrder
     */
    public function setServiceID(int $ServiceID)
    {
        $this->ServiceID = $ServiceID;
        return $this;
    }

    /**
     * @return string
     */
    public function getToLatitude()
    {
        return $this->ToLatitude;
    }

    /**
     * @param string $ToLatitude
     * @return GHNOrder
     */
    public function setToLatitude(string $ToLatitude)
    {
        $this->ToLatitude = $ToLatitude;
        return $this;
    }

    /**
     * @return string
     */
    public function getToLongitude()
    {
        return $this->ToLongitude;
    }

    /**
     * @param string $ToLongitude
     * @return GHNOrder
     */
    public function setToLongitude(string $ToLongitude)
    {
        $this->ToLongitude = $ToLongitude;
        return $this;
    }

    /**
     * @return string
     */
    public function getFromLat()
    {
        return $this->FromLat;
    }

    /**
     * @param string $FromLat
     * @return GHNOrder
     */
    public function setFromLat(string $FromLat)
    {
        $this->FromLat = $FromLat;
        return $this;
    }

    /**
     * @return string
     */
    public function getFromLng()
    {
        return $this->FromLng;
    }

    /**
     * @param string $FromLng
     * @return GHNOrder
     */
    public function setFromLng(string $FromLng)
    {
        $this->FromLng = $FromLng;
        return $this;
    }

    /**
     * @return string
     */
    public function getContent()
    {
        return $this->Content;
    }

    /**
     * @param string $Content
     * @return GHNOrder
     */
    public function setContent(string $Content)
    {
        $this->Content = $Content;
        return $this;
    }

    /**
     * @return string
     */
    public function getCouponCode()
    {
        return $this->CouponCode;
    }

    /**
     * @param string $CouponCode
     * @return GHNOrder
     */
    public function setCouponCode(string $CouponCode)
    {
        $this->CouponCode = $CouponCode;
        return $this;
    }

    /**
     * @return float
     */
    public function getWeight()
    {
        return $this->Weight;
    }

    /**
     * @param float $Weight
     * @return GHNOrder
     */
    public function setWeight(float $Weight)
    {
        $this->Weight = $Weight;
        return $this;
    }

    /**
     * @return float
     */
    public function getLength()
    {
        return $this->Length;
    }

    /**
     * @param float $Length
     * @return GHNOrder
     */
    public function setLength(float $Length)
    {
        $this->Length = $Length;
        return $this;
    }

    /**
     * @return float
     */
    public function getWidth()
    {
        return $this->Width;
    }

    /**
     * @param float $Width
     * @return GHNOrder
     */
    public function setWidth(float $Width)
    {
        $this->Width = $Width;
        return $this;
    }

    /**
     * @return float
     */
    public function getHeight()
    {
        return $this->Height;
    }

    /**
     * @param float $Height
     * @return GHNOrder
     */
    public function setHeight(float $Height)
    {
        $this->Height = $Height;
        return $this;
    }

    /**
     * @return bool
     */
    public function isCheckMainBankAccount()
    {
        return $this->CheckMainBankAccount;
    }

    /**
     * @param bool $CheckMainBankAccount
     * @return GHNOrder
     */
    public function setCheckMainBankAccount(bool $CheckMainBankAccount)
    {
        $this->CheckMainBankAccount = $CheckMainBankAccount;
        return $this;
    }

    /**
     * @return string
     */
    public function getShippingOrderCosts()
    {
        return $this->ShippingOrderCosts;
    }

    /**
     * @param string $ShippingOrderCosts
     * @return GHNOrder
     */
    public function setShippingOrderCosts(string $ShippingOrderCosts)
    {
        $this->ShippingOrderCosts = $ShippingOrderCosts;
        return $this;
    }

    /**
     * @return string
     */
    public function getReturnContactName()
    {
        return $this->ReturnContactName;
    }

    /**
     * @param string $ReturnContactName
     * @return GHNOrder
     */
    public function setReturnContactName(string $ReturnContactName)
    {
        $this->ReturnContactName = $ReturnContactName;
        return $this;
    }

    /**
     * @return string
     */
    public function getReturnContactPhone()
    {
        return $this->ReturnContactPhone;
    }

    /**
     * @param string $ReturnContactPhone
     * @return GHNOrder
     */
    public function setReturnContactPhone(string $ReturnContactPhone)
    {
        $this->ReturnContactPhone = $ReturnContactPhone;
        return $this;
    }

    /**
     * @return string
     */
    public function getReturnAddress()
    {
        return $this->ReturnAddress;
    }

    /**
     * @param string $ReturnAddress
     * @return GHNOrder
     */
    public function setReturnAddress(string $ReturnAddress)
    {
        $this->ReturnAddress = $ReturnAddress;
        return $this;
    }

    /**
     * @return int
     */
    public function getReturnDistrictID()
    {
        return $this->ReturnDistrictID;
    }

    /**
     * @param int $ReturnDistrictID
     * @return GHNOrder
     */
    public function setReturnDistrictID(int $ReturnDistrictID)
    {
        $this->ReturnDistrictID = $ReturnDistrictID;
        return $this;
    }

    /**
     * @return string
     */
    public function getExternalReturnCode()
    {
        return $this->ExternalReturnCode;
    }

    /**
     * @param string $ExternalReturnCode
     * @return GHNOrder
     */
    public function setExternalReturnCode(string $ExternalReturnCode)
    {
        $this->ExternalReturnCode = $ExternalReturnCode;
        return $this;
    }

    /**
     * @return bool
     */
    public function isCreditCreate()
    {
        return $this->IsCreditCreate;
    }

    /**
     * @param bool $IsCreditCreate
     * @return GHNOrder
     */
    public function setIsCreditCreate(bool $IsCreditCreate)
    {
        $this->IsCreditCreate = $IsCreditCreate;
        return $this;
    }

    /**
     * @return int
     */
    public function getAffiliateID()
    {
        return $this->AffiliateID;
    }

    /**
     * @param int $AffiliateID
     * @return GHNOrder
     */
    public function setAffiliateID(int $AffiliateID)
    {
        $this->AffiliateID = $AffiliateID;
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
     * @return GHNOrder
     */
    public function setId(int $id)
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
     * @return GHNOrder
     */
    public function setShipping(Shipping $Shipping)
    {
        $this->Shipping = $Shipping;
        if (empty($this->Order)) {
            $this->Order = $Shipping->getOrder();
        }

        return $this;
    }

    /**
     * @param bool $isUnserialize
     * @return mixed|string
     */
    public function getReturnData($isUnserialize = false)
    {
        if ($isUnserialize) {
            return unserialize($this->returnData);
        }

        return $this->returnData;
    }

    /**
     * @param string $returnData serialize
     */
    public function setReturnData(string $returnData)
    {
        $this->returnData = $returnData;
        $data = unserialize($returnData);
        if (isset($data['OrderCode'])) {
            $this->setOrderCode($data['OrderCode']);
        }

        return $this;
    }

    public function isCreatedOrder()
    {
        if (!empty($this->returnData)) {
            $data = unserialize($this->returnData);
            if (empty($data['ErrorMessage']) && $data['OrderCode'] && $data['OrderID']) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param GHNConfig $config
     * @param null $ghn_affiliate_id
     * @return array
     */
    public function buildCreateOrderData(GHNConfig $config, $ghn_affiliate_id = null)
    {
        $this->token = $config->getToken();
        $this->PaymentTypeID = (int)$config->getPaymentType();
        $this->FromDistrictID = (int) $this->GHNWarehouse->getGHNPref()->getDistrictId();
        $this->ToDistrictID = (int) $this->Shipping->getGHNPref()->getDistrictId();
        $this->ClientContactName = $config->getClientName();
        $this->ClientContactPhone = $config->getClientPhone();
        $this->ClientAddress = $config->getClientAddress();
        $this->CustomerName = $this->Shipping->getFullName();
        $this->CustomerPhone = $this->Shipping->getOrder()->getPhoneNumber();
        $this->ShippingAddress = $this->Shipping->getGHNFullAddress();
        $this->NoteCode = $config->getNoteCode();
        $this->Note = $this->Shipping->getNote();
        $this->ClientHubID = (int) $this->GHNWarehouse->getHubId();
        $this->ServiceID = (int) $this->GHNService->getMainServiceId();
        $this->Weight = (int) $config->getWeight();
        $this->Height = (int) $config->getHeight();
        $this->Length = (int) $config->getLength();
        $this->Width = (int) $config->getWidth();
        $this->InsuranceFee = (int) $config->getInsuranceFee();
        $this->CheckMainBankAccount = (bool) $config->isCheckMainBankAccount();
        $this->ReturnContactName = $this->GHNWarehouse->getContactName();
        $this->ReturnContactPhone = $this->GHNWarehouse->getContactPhone();
        $this->ReturnAddress = $this->GHNWarehouse->getAddress();
        $this->ReturnDistrictID = (int) $this->GHNWarehouse->getGHNPref()->getDistrictId();
        $this->ExternalReturnCode = $this->GHNWarehouse->getExternalReturnCode($this->Shipping);
        $this->IsCreditCreate = (bool) $config->isCreditCreate();
        $this->AffiliateID = $ghn_affiliate_id;

        // todo: check payment method : $this->Order->getPaymentMethod()
        if (!$this->Order->isGHNCOD()) {
            $this->CoDAmount = (int) $this->Order->getPaymentTotal();
            $this->Order->setIsGHNCOD(true);
        }

        $arrAttr = $this->toArray(['GHNService', 'GHNWarehouse', 'Order', 'Shipping', 'id', 'returnData', '__initializer__', '__cloner__', '__isInitialized__', 'AnnotationReader']);
        // remove all empty data
        foreach ($arrAttr as $key => $att) {
            if (is_null($att) || is_object($att)) {
                unset($arrAttr[$key]);
            }
        }

        return $arrAttr;
    }

    /**
     * @param GHNConfig $config
     * @return array
     */
    public function buildUpdateOrderData(GHNConfig $config)
    {
        if (!$this->isCreatedOrder()) {
            return [];
        }

        $this->token = $config->getToken();
        $this->PaymentTypeID = (int)$config->getPaymentType();
        $this->FromDistrictID = (int) $this->GHNWarehouse->getGHNPref()->getDistrictId();
        $this->ToDistrictID = (int) $this->Shipping->getGHNPref()->getDistrictId();
        $this->ClientContactName = $config->getClientName();
        $this->ClientContactPhone = $config->getClientPhone();
        $this->ClientAddress = $config->getClientAddress();
        $this->CustomerName = $this->Shipping->getFullName();
        $this->CustomerPhone = $this->Shipping->getOrder()->getPhoneNumber();
        $this->ShippingAddress = $this->Shipping->getGHNFullAddress();
        $this->NoteCode = $config->getNoteCode();
        $this->Note = $this->Shipping->getNote();
        $this->ClientHubID = (int) $this->GHNWarehouse->getHubId();
        $this->ServiceID = (int) $this->GHNService->getMainServiceId();
        $this->Weight = (int) $config->getWeight();
        $this->Height = (int) $config->getHeight();
        $this->Length = (int) $config->getLength();
        $this->Width = (int) $config->getWidth();
        $this->InsuranceFee = (int) $config->getInsuranceFee();
        $this->ReturnContactName = $this->GHNWarehouse->getContactName();
        $this->ReturnContactPhone = $this->GHNWarehouse->getContactPhone();
        $this->ReturnAddress = $this->GHNWarehouse->getAddress();
        $this->ReturnDistrictID = (int) $this->GHNWarehouse->getGHNPref()->getDistrictId();

        // todo: check payment method : $this->Order->getPaymentMethod()
        if (!$this->Order->isGHNCOD()) {
            $this->CoDAmount = (int) $this->Order->getPaymentTotal();
            $this->Order->setIsGHNCOD(true);
        }

        $arrAttr = $this->toArray(['GHNService', 'GHNWarehouse', 'Order', 'Shipping', 'id', 'returnData', '__initializer__', '__cloner__', '__isInitialized__', 'AnnotationReader']);
        // remove all empty data
        foreach ($arrAttr as $key => $att) {
            if (is_null($att)) {
                unset($arrAttr[$key]);
            }
        }
        unset($arrAttr['CheckMainBankAccount']);
        unset($arrAttr['ExternalReturnCode']);
        // return district code? ReturnDistrictCode

        $data = unserialize($this->returnData);
        // update require
        $arrAttr['ShippingOrderID'] = $data['OrderID'];
        $arrAttr['OrderCode'] = $data['OrderCode'];

        return $arrAttr;
    }

    /**
     * @return GHNService
     */
    public function getGHNService()
    {
        return $this->GHNService;
    }

    /**
     * @param GHNService $GHNService
     * @return GHNOrder
     */
    public function setGHNService(GHNService $GHNService)
    {
        $this->GHNService = $GHNService;
        return $this;
    }

    /**
     * @return GHNWarehouse
     */
    public function getGHNWarehouse()
    {
        return $this->GHNWarehouse;
    }

    /**
     * @param GHNWarehouse $GHNWarehouse
     * @return GHNOrder
     */
    public function setGHNWarehouse(GHNWarehouse $GHNWarehouse)
    {
        $this->GHNWarehouse = $GHNWarehouse;
        return $this;
    }

    /**
     * @return string
     */
    public function getOrderCode()
    {
        return $this->OrderCode;
    }

    /**
     * @param string $OrderCode
     */
    public function setOrderCode(string $OrderCode)
    {
        $this->OrderCode = $OrderCode;
    }

    /**
     * @param GHNConfig $config
     * @return array
     */
    public function buildCancelData(GHNConfig $config)
    {
        $arr = [
            'token' => $config->getToken(),
            'OrderCode' => $this->getOrderCode()
        ];

        return $arr;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param string $status
     * @return $this
     */
    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }

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
     * @return GHNOrderCallback[]
     */
    public function getGHNOrderCallbacks()
    {
        return $this->GHNOrderCallbacks;
    }

    /**
     * @param GHNOrderCallback $GHNOrderCallbacks
     * @return GHNOrder
     */
    public function addGHNOrderCallbacks(GHNOrderCallback $GHNOrderCallback)
    {
        $this->GHNOrderCallbacks->add($GHNOrderCallback);
        return $this;
    }

    /**
     * @param GHNOrderCallback $GHNOrderCallback
     * @return GHNOrder
     */
    public function removeGHNOrderCallbacks(GHNOrderCallback $GHNOrderCallback)
    {
        $this->GHNOrderCallbacks->removeElement($GHNOrderCallback);
        return $this;
    }
}
