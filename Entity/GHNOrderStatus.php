<?php
/**
 * Author: lqdung1992@gmail.com
 * Date: 2/28/2019
 * Time: 1:38 PM
 */

namespace Plugin\OSGHNDelivery\Entity;


use Eccube\Entity\AbstractEntity;

class GHNOrderStatus extends AbstractEntity
{
    const READY_TO_PICK = 'ReadyToPick';
    const PICKING = 'Picking';
    const CANCEL = 'Cancel';
    const STORING = 'Storing';
    const DELIVERING = 'Delivering';
    const RETURN = 'Return';
    const RETURNED = 'Returned';
    const DELIVERED = 'Delivered';
    const WAIT_TO_FINISH = 'WaitToFinish';
    const FINISH = 'Finish';
}
