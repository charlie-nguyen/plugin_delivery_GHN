<?php
/**
 * Author: lqdung1992@gmail.com
 * Date: 2/20/2019
 * Time: 4:50 PM
 */

namespace Plugin\OSGHNDelivery\Entity;

use Eccube\Annotation as Eccube;

/**
 * Trait BaseInfoTrait
 * @package Plugin\OSGHNDelivery\Entity
 *
 * @Eccube\EntityExtension("Eccube\Entity\BaseInfo")
 */
trait BaseInfoTrait
{
    use FullAddressTrait;
}