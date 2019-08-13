<?php
/**
 * Author: lqdung1992@gmail.com
 * Date: 2/14/2019
 * Time: 4:58 PM
 */

namespace Plugin\OSGHNDelivery\Entity;

use Eccube\Entity\AbstractEntity;
use Doctrine\ORM\Mapping as ORM;
use Eccube\Entity\Shipping;

/**
 * Class Warehouse
 *
 * @package Plugin\OSGHNDelivery\Entity
 *
 * @ORM\Table(name="plg_ghn_warehouse")
 * @ORM\Entity(repositoryClass="Plugin\OSGHNDelivery\Repository\GHNWarehouseRepository")
 */
class GHNWarehouse extends AbstractEntity
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
     * @var string|null
     *
     * @ORM\Column(name="address", type="string", length=255)
     */
    private $address;

    /**
     * @var string|null
     *
     * @ORM\Column(name="contact_name", type="string", length=255)
     */
    private $contact_name;

    /**
     * @var string|null
     *
     * @ORM\Column(name="contact_phone", type="string", length=30)
     */
    private $contact_phone;

    /**
     * @var GHNPref
     *
     * @ORM\ManyToOne(targetEntity="Plugin\OSGHNDelivery\Entity\GHNPref", inversedBy="Warehouses")
     * @ORM\JoinColumns(
     *     @ORM\JoinColumn(name="ghn_pref_id", referencedColumnName="id")
     * )
     */
    private $GHNPref;

    /**
     * @var string|null
     *
     * @ORM\Column(name="longitude", type="string", length=255, nullable=true)
     */
    private $long;

    /**
     * @var string|null
     *
     * @ORM\Column(name="latitude", type="string", length=255, nullable=true)
     */
    private $lati;

    /**
     * @var string|null
     *
     * @ORM\Column(name="email", type="string", length=255, nullable=true)
     */
    private $email;

    /**
     * @var string|null
     *
     * @ORM\Column(name="is_main", type="boolean", nullable=true)
     */
    private $is_main;

    /**
     * @var int
     *
     * @ORM\Column(name="hub_id", type="integer", nullable=true)
     */
    private $hub_id;

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
     * GHNWarehouse constructor.
     * @param int $hub_id
     */
    public function __construct()
    {
        $this->is_main = true;
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
     * @return null|string
     */
    public function getAddress(): ?string
    {
        return $this->address;
    }

    /**
     * @param null|string $address
     */
    public function setAddress(?string $address): void
    {
        $this->address = $address;
    }

    /**
     * @return null|string
     */
    public function getContactName(): ?string
    {
        return $this->contact_name;
    }

    /**
     * @param null|string $contact_name
     */
    public function setContactName(?string $contact_name): void
    {
        $this->contact_name = $contact_name;
    }

    /**
     * @return null|string
     */
    public function getContactPhone(): ?string
    {
        return $this->contact_phone;
    }

    /**
     * @param null|string $contact_phone
     */
    public function setContactPhone(?string $contact_phone): void
    {
        $this->contact_phone = $contact_phone;
    }

    /**
     * @return GHNPref
     */
    public function getGHNPref(): ?GHNPref
    {
        return $this->GHNPref;
    }

    /**
     * @param GHNPref $GHNPref
     */
    public function setGHNPref(GHNPref $GHNPref): void
    {
        $this->GHNPref = $GHNPref;
    }

    /**
     * @return null|string
     */
    public function getLong(): ?string
    {
        return $this->long;
    }

    /**
     * @param null|string $long
     */
    public function setLong(?string $long): void
    {
        $this->long = $long;
    }

    /**
     * @return null|string
     */
    public function getLati(): ?string
    {
        return $this->lati;
    }

    /**
     * @param null|string $lati
     */
    public function setLati(?string $lati): void
    {
        $this->lati = $lati;
    }

    /**
     * @return null|string
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @param null|string $email
     */
    public function setEmail(?string $email): void
    {
        $this->email = $email;
    }

    /**
     * @return null|string
     */
    public function getisMain(): ?string
    {
        return $this->is_main;
    }

    /**
     * @param null|string $is_main
     */
    public function setIsMain(?string $is_main): void
    {
        $this->is_main = $is_main;
    }

    /**
     * @return int
     */
    public function getHubId()
    {
        return $this->hub_id;
    }

    /**
     * @param int $hub_id
     */
    public function setHubId(int $hub_id): void
    {
        $this->hub_id = $hub_id;
    }

    public function getApiCreateParameter()
    {
        $ret = [
            'Address' => $this->getAddress(),
            'ContactName' => $this->getContactName(),
            'ContactPhone' => $this->getContactPhone(),
            'DistrictID' => $this->getGHNPref() ? (int) $this->getGHNPref()->getDistrictId() : null,
            'IsMain' => true, // tmp
        ];

        if ($this->getEmail()) {
            $ret['Email'] = $this->getEmail();
        }

        if ($this->getLati()) {
            $ret['Latitude'] = (float) $this->getLati();
        }

        if ($this->getLong()) {
            $ret['Longitude'] = (float) $this->getLong();
        }

        return $ret;
    }

    /**
     * @param Shipping $shipping
     * @return string
     */
    public function getExternalReturnCode(Shipping $shipping)
    {
        $ret = [
            $this->getId(),
            $this->getHubId(),
            $this->getGHNPref()->getDistrictId(),
            $shipping->getId()
        ];

        return implode('', $ret);
    }

    /**
     * @param array $warehouse
     * @param GHNPref|null $GHNPref
     */
    public function setWarehouseFromApiData(array $warehouse, GHNPref $GHNPref = null)
    {
        $this->setHubId($warehouse['HubID']);
        $this->setEmail($warehouse['Email']);
        $this->setLati($warehouse['Latitude']);
        $this->setLong($warehouse['Longitude']);
        $this->setAddress($warehouse['Address']);
        $this->setContactName($warehouse['ContactName']);
        $this->setContactPhone($warehouse['ContactPhone']);
        $this->setIsMain(true);
        if ($GHNPref) {
            $this->setGHNPref($GHNPref);
            $GHNPref->addWarehouse($this);
        }
    }
}
