<?php

namespace Plugin\OSGHNDelivery;

use Eccube\Common\EccubeNav;

class Nav implements EccubeNav
{
    /**
     * @return array
     */
    public static function getNav()
    {
        return [
            'product' => [
                'children' => [
                    'ghn_delivery_admin_warehouse' => [
                        'url' => 'ghn_delivery_admin_warehouse',
                        'name' => 'ghn.warehouse',
                    ],
                ],
            ],
        ];
    }
}
