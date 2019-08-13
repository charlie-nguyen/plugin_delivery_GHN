<?php
/**
 * Author: lqdung1992@gmail.com
 * Date: 3/7/2019
 * Time: 11:07 AM
 */

namespace Plugin\OSGHNDelivery\Service;


use Throwable;

class GHNException extends \Exception
{
    public function __construct(string $message = 'ghn.api.call.not_config', int $code = 0, Throwable $previous = null)
    {
        $message = trans($message);
        parent::__construct($message, $code, $previous);
    }
}
