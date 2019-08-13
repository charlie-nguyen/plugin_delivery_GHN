<?php
/**
 * Author: lqdung1992@gmail.com
 * Date: 2/20/2019
 * Time: 3:33 PM
 */

namespace Plugin\OSGHNDelivery\Entity;


use Eccube\Entity\Master\Pref;

/**
 * Trait FullAddressTrait
 * @package Plugin\OSGHNDelivery\Entity
 *
 * @internal Eccube\Entity\Shipping|Eccube\Entity\Order|Eccube\Entity\Customer
 *
 * @property string $addr01
 * @property string $addr02
 * @property Pref $Pref
 * @property string $postal_code
 */
trait FullAddressTrait
{
    /**
     * @param bool $isNeedPostcode
     * @return string
     */
    public function getGHNFullAddress($isNeedPostcode = false)
    {
        $tmp = [$this->addr02, $this->addr01, $this->Pref];

        if ($isNeedPostcode) {
            $tmp[] = $this->postal_code;
        }

        $tmp = array_filter($tmp, function ($item) {
            return empty($item) ? false : true;
        });

        return implode(', ', $tmp);
    }
}