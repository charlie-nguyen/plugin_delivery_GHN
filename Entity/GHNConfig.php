<?php

namespace Plugin\OSGHNDelivery\Entity;

use Doctrine\ORM\Mapping as ORM;
use Eccube\Entity\AbstractEntity;

/**
 * Config
 *
 * @ORM\Table(name="plg_ghn_delivery_config")
 * @ORM\Entity(repositoryClass="Plugin\OSGHNDelivery\Repository\GHNConfigRepository")
 */
class GHNConfig extends AbstractEntity
{
    CONST PAYMENT_TYPE_SELLER = 1;
    CONST PAYMENT_TYPE_BUYER = 2;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", options={"unsigned":true})
     * @ORM\Id
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="client_id", type="string", length=255, nullable=false)
     */
    private $client_id;

    /**
     * @var string
     *
     * @ORM\Column(name="client_name", type="string", length=255, nullable=false)
     */
    private $client_name;

    /**
     * @var string
     *
     * @ORM\Column(name="client_phone", type="string", length=20, nullable=false)
     */
    private $client_phone;

    /**
     * @var string
     *
     * @ORM\Column(name="client_address", type="string", length=255, nullable=false)
     */
    private $client_address;

    /**
     * @var string
     *
     * @ORM\Column(name="token", type="string", length=255, nullable=false)
     */
    private $token;

    /**
     * @var float
     *
     * @ORM\Column(name="weight", type="float", precision=1, nullable=false)
     */
    private $weight;

    /**
     * @var float
     *
     * @ORM\Column(name="length", type="float", precision=1, nullable=false)
     */
    private $length;

    /**
     * @var float
     *
     * @ORM\Column(name="width", type="float", precision=1, nullable=false)
     */
    private $width;

    /**
     * @var float
     *
     * @ORM\Column(name="height", type="float", precision=1, nullable=false)
     */
    private $height;

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
    private $payment_type = 1;

    /**
     * @var string
     *
     * @ORM\Column(name="callback_url", type="string", length=2000)
     */
    private $callback_url;

    /**
     * Allowed values: CHOTHUHANG, CHOXEMHANGKHONGTHU, KHONGCHOXEMHANG
     *
     * @var string
     *
     * @ORM\Column(name="note_code", type="string", length=255, nullable=false)
     */
    private $note_code;

    /**
     * @var float
     *
     * @ORM\Column(name="insurance_fee", type="float", precision=0)
     */
    private $insurance_fee = 0;

    /**
     * Check if this account have input bank account information. GHN use bank account to transfer COD.
     *
     * @var bool
     *
     * @ORM\Column(name="check_main_bank_account", type="boolean")
     */
    private $check_main_bank_account = false;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_credit_create", type="boolean")
     */
    private $is_credit_create = false;

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
     * @var boolean
     *
     * @ORM\Column(name="is_prod", type="boolean", nullable=true)
     */
    private $is_prod = false;

    /**
     * @return boolean
     */
    public function isProd()
    {
        return $this->is_prod;
    }

    /**
     * @param boolean $is_prod
     * @return GHNConfig
     */
    public function setIsProd($is_prod)
    {
        $this->is_prod = $is_prod;
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
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getClientPhone()
    {
        return $this->client_phone;
    }

    /**
     * @param mixed $client_phone
     * @return GHNConfig
     */
    public function setClientPhone($client_phone)
    {
        $this->client_phone = $client_phone;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getClientAddress()
    {
        return $this->client_address;
    }

    /**
     * @param mixed $client_address
     * @return GHNConfig
     */
    public function setClientAddress($client_address)
    {
        $this->client_address = $client_address;
        return $this;
    }

    /**
     * @return string
     */
    public function getClientId()
    {
        return $this->client_id;
    }

    /**
     * @param string $client_id
     */
    public function setClientId(string $client_id)
    {
        $this->client_id = $client_id;

        return $this;
    }

    /**
     * @return string
     */
    public function getClientName()
    {
        return $this->client_name;
    }

    /**
     * @param string $client_name
     */
    public function setClientName($client_name)
    {
        $this->client_name = $client_name;

        return $this;
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
     */
    public function setToken($token)
    {
        $this->token = $token;

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
     */
    public function setWeight(float $weight)
    {
        $this->weight = $weight;

        return $this;
    }

    /**
     * @return string
     */
    public function getCallbackUrl()
    {
        return $this->callback_url;
    }

    /**
     * @param string $callback_url
     */
    public function setCallbackUrl(string $callback_url)
    {
        $this->callback_url = $callback_url;

        return $this;
    }

    /**
     * @return string
     */
    public function getNoteCode()
    {
        return $this->note_code;
    }

    /**
     * @param string $note_code
     */
    public function setNoteCode(string $note_code)
    {
        $this->note_code = $note_code;
    }

    /**
     * @return float
     */
    public function getInsuranceFee()
    {
        return $this->insurance_fee;
    }

    /**
     * @param float $insurance_fee
     */
    public function setInsuranceFee(float $insurance_fee)
    {
        $this->insurance_fee = $insurance_fee;

        return $this;
    }

    /**
     * @return bool
     */
    public function isCheckMainBankAccount()
    {
        return $this->check_main_bank_account;
    }

    /**
     * @param bool $check_main_bank_account
     */
    public function setCheckMainBankAccount(bool $check_main_bank_account)
    {
        $this->check_main_bank_account = $check_main_bank_account;

        return $this;
    }

    /**
     * @return bool
     */
    public function isCreditCreate()
    {
        return $this->is_credit_create;
    }

    /**
     * @param bool $is_credit_create
     */
    public function setIsCreditCreate(bool $is_credit_create)
    {
        $this->is_credit_create = $is_credit_create;

        return $this;
    }

    /**
     * @return int
     */
    public function getPaymentType()
    {
        return $this->payment_type;
    }

    /**
     * @param int $payment_type
     */
    public function setPaymentType(int $payment_type)
    {
        $this->payment_type = $payment_type;

        return $this;
    }

    /**
     * @return float
     */
    public function getLength()
    {
        return $this->length;
    }

    /**
     * @param float $length
     * @return GHNConfig
     */
    public function setLength(float $length)
    {
        $this->length = $length;
        return $this;
    }

    /**
     * @return float
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * @param float $width
     * @return GHNConfig
     */
    public function setWidth(float $width)
    {
        $this->width = $width;
        return $this;
    }

    /**
     * @return float
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * @param float $height
     * @return GHNConfig
     */
    public function setHeight(float $height)
    {
        $this->height = $height;
        return $this;
    }

    public function getConfigCallbackData()
    {
        $arrRet = [
            'token' => $this->getToken(),
            'TokenClient' => [$this->getToken()],
            'ConfigCod' => true,
            'ConfigReturnData' => true,
            'URLCallback' => $this->getCallbackUrl(),
            'ConfigField' =>
                [
                    'CoDAmount' => true,
                    'CurrentWarehouseName' => true,
                    'CustomerID' => true,
                    'CustomerName' => true,
                    'CustomerPhone' => true,
                    'Note' => true,
                    'OrderCode' => true,
                    'ServiceName' => true,
                    'ShippingOrderCosts' => true,
                    'Weight' => true,
                    'ExternalCode' => true,
                    'ReturnInfo' => true,
                ],
            'ConfigStatus' =>
                [
                    'ReadyToPick' => true,
                    'Picking' => true,
                    'Storing' => true,
                    'Delivering' => true,
                    'Delivered' => true,
                    'WaitingToFinish' => true,
                    'Return' => true,
                    'Returned' => true,
                    'Finish' => true,
                    'LostOrder' => true,
                    'Cancel' => true,
                ],
        ];
        return $arrRet;
    }
}
