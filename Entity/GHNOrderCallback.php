<?php
/**
 * Author: lqdung1992@gmail.com
 * Date: 2/28/2019
 * Time: 2:08 PM
 */

namespace Plugin\OSGHNDelivery\Entity;


use Eccube\Entity\AbstractEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class GHNOrderCallback
 * @package Plugin\OSGHNDelivery\Entity
 *
 * @ORM\Table(name="plg_ghn_order_callback")
 * @ORM\Entity(repositoryClass="Plugin\OSGHNDelivery\Repository\GHNOrderCallbackRepository")
 */
class GHNOrderCallback extends AbstractEntity
{
    const STATUS_CALLBACK = 1;
    const COD_CALLBACK = 2;
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", options={"unsigned":true})
     * @ORM\Id
     * @ORM\GeneratedValue()
     */
    private $id;

    /**
     * @var GHNOrder
     *
     * @ORM\ManyToOne(targetEntity="Plugin\OSGHNDelivery\Entity\GHNOrder", inversedBy="GHNOrderCallbacks")
     * @ORM\JoinColumn(name="ghn_order_id", referencedColumnName="id", nullable=true)
     */
    private $GHNOrder;

    /**
     * @var string|null
     *
     * @ORM\Column(name="callback_data", type="text")
     */
    private $callback_data;

    /**
     * @var string|null
     *
     * @ORM\Column(name="header", type="text")
     */
    private $header;

    /**
     * @var string|null
     *
     * @ORM\Column(name="type", type="smallint")
     */
    private $type;

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
     * @return mixed
     */
    public function getCallbackData($isUnserialize = false)
    {
        if ($isUnserialize) {
            return unserialize($this->callback_data);
        }
        return $this->callback_data;
    }

    /**
     * @param null|string $callback_data
     * @return GHNOrderCallback
     */
    public function setCallbackData(?string $callback_data)
    {
        $this->callback_data = $callback_data;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getHeader($isUnserialize = false)
    {
        if ($isUnserialize) {
            return unserialize($this->header);
        }
        return $this->header;
    }

    /**
     * @param null|string $header
     * @return GHNOrderCallback
     */
    public function setHeader(?string $header)
    {
        $this->header = $header;
        return $this;
    }

    /**
     * @return GHNOrder
     */
    public function getGHNOrder()
    {
        return $this->GHNOrder;
    }

    /**
     * @param GHNOrder $GHNOrder
     * @return GHNOrderCallback
     */
    public function setGHNOrder(GHNOrder $GHNOrder)
    {
        $this->GHNOrder = $GHNOrder;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getType(): ?string
    {
        return $this->type;
    }

    /**
     * @param null|string $type
     * @return GHNOrderCallback
     */
    public function setType(?string $type)
    {
        $this->type = $type;
        return $this;
    }
}
