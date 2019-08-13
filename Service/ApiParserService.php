<?php
/**
 * Author: lqdung1992@gmail.com
 * Date: 2/15/2019
 * Time: 2:24 PM
 */

namespace Plugin\OSGHNDelivery\Service;


class ApiParserService
{
    /**
     * @var integer|null
     */
    private $code;
    /**
     * @var string|null
     */
    private $msg;
    /**
     * @var array
     */
    private $data;

    /**
     * ApiParserService constructor.
     * @param array $data
     */
    public function __construct()
    {
        $this->data = array();
    }

    /**
     * @return int|null
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @return null|string
     */
    public function getMsg()
    {
        return $this->msg;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param array|Json $retData
     */
    public function parse($retData)
    {
        if (!is_array($retData)) {
            $retData = json_decode($retData, true);
        }

        if (isset($retData['code']) && !is_null($retData['code'])) {
            $this->code = $retData['code'];
        }

        if (isset($retData['msg']) && !is_null($retData['msg'])) {
            $this->msg = $retData['msg'];
        }

        if (isset($retData['data']) && !is_null($retData['data'])) {
            $this->data = $retData['data'];
        }
    }

}