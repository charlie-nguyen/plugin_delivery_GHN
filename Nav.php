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
            'plugin' => [
                'children' => [
                    'OSGHNDelivery' => [
                        'name' => 'ghn.name',
                        'children' => [
                            'index' => [
                                'name' => 'ghn.header',
                                'url' => 'ghn_delivery_admin_warehouse',
                            ]
                        ]
                    ],
                ],
            ],
        ];
    }
}
