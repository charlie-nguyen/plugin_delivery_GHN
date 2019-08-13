<?php
/**
 * Author: lqdung1992@gmail.com
 * Date: 2/14/2019
 * Time: 5:34 PM
 */

namespace Plugin\OSGHNDelivery\Service;


use Eccube\Common\Constant;
use Eccube\Common\EccubeConfig;
use Eccube\Entity\BaseInfo;
use Eccube\Repository\BaseInfoRepository;
use Plugin\OSGHNDelivery\Entity\GHNConfig;
use Plugin\OSGHNDelivery\Entity\GHNOrder;
use Plugin\OSGHNDelivery\Entity\GHNWarehouse;
use Plugin\OSGHNDelivery\Repository\GHNConfigRepository;
use Symfony\Component\HttpFoundation\RequestStack;

class ApiService
{
    /**
     * @var BaseInfo
     */
    protected $baseInfo;

    /**
     * @var RequestStack
     */
    protected $requestStack;

    /**
     * @var string
     */
    private $endpoint;

    /**
     * @var GHNConfig
     */
    protected $config;

    /**
     * ApiService constructor.
     * @param GHNConfigRepository $configRepo
     * @param EccubeConfig $eccubeConfig
     * @param BaseInfoRepository $baseInfoRepository
     * @param RequestStack $requestStack
     * @throws \Exception
     */
    public function __construct(GHNConfigRepository $configRepo, EccubeConfig $eccubeConfig, BaseInfoRepository $baseInfoRepository, RequestStack $requestStack)
    {
        $this->config = $configRepo->find(1);
        $this->baseInfo = $baseInfoRepository->get();
        $this->requestStack = $requestStack;

        $this->endpoint = $eccubeConfig->get('ghn_endpoint_test');
        if ($this->config && $this->config->isProd()) {
            $this->endpoint = $eccubeConfig->get('ghn_endpoint_prod');
        }
    }

    /**
     * @param GHNConfig $config
     * @return ApiParserService
     */
    public function updateConfig(GHNConfig $config)
    {
        $url = $this->endpoint . '/SetConfigClient';
        $data = $config->getConfigCallbackData();

        $jsonRet = $this->requestApi($url, $data, true);
        $parser = new ApiParserService();
        $parser->parse($jsonRet);

        return $parser;
    }

    /**
     * @param GHNWarehouse $GHNWarehouse
     * @return ApiParserService
     * @throws GHNException
     */
    public function addWarehouse(GHNWarehouse $GHNWarehouse)
    {
        $this->checkConfig();
        $url = $this->endpoint . '/AddHubs';

        $data = $GHNWarehouse->getApiCreateParameter();
        $data['token'] = $this->config->getToken();

        $jsonRet = $this->requestApi($url, $data, true);
        $parser = new ApiParserService();
        $parser->parse($jsonRet);

        return $parser;
    }

    /**
     * get all warehouse of customer (by token)
     *
     * @return ApiParserService
     * @throws GHNException
     */
    public function getWarehouse()
    {
        $this->checkConfig();
        $url = $this->endpoint . '/GetHubs';

        $data['token'] = $this->config->getToken();

        $jsonRet = $this->requestApi($url, $data, true);
        $parser = new ApiParserService();
        $parser->parse($jsonRet);

        return $parser;
    }


    /**
     * @param GHNWarehouse $GHNWarehouse
     * @return ApiParserService
     * @throws GHNException
     */
    public function updateWarehouse(GHNWarehouse $GHNWarehouse)
    {
        $this->checkConfig();

        $url = $this->endpoint . '/UpdateHubs';

        $data = $GHNWarehouse->getApiCreateParameter();
        $data['token'] = $this->config->getToken();
        $data['HubID'] = (int) $GHNWarehouse->getHubId();

        $jsonRet = $this->requestApi($url, $data, true);
        $parser = new ApiParserService();
        $parser->parse($jsonRet);

        return $parser;
    }

    /**
     * @param $fromDistrictId
     * @param $toDistrictId
     * @param array $options
     * @return ApiParserService
     * @throws GHNException
     */
    public function findAvailableServices($fromDistrictId, $toDistrictId, $options = array())
    {
        $this->checkConfig();

        $url = $this->endpoint . '/FindAvailableServices';

        $data['token'] = $this->config->getToken();
        $data['FromDistrictID'] = (int) $fromDistrictId;
        $data['ToDistrictID'] = (int) $toDistrictId;
        if (count($options)) {
            foreach ($options as $key => $option) {
                $data[$key] = $option;
            }
        }

        $jsonRet = $this->requestApi($url, $data, true);
        $parser = new ApiParserService();
        $parser->parse($jsonRet);

        return $parser;
    }

    /**
     * @param GHNOrder $GHNOrder
     * @param $affId
     * @return ApiParserService
     * @throws GHNException
     */
    public function createGHNOrder(GHNOrder $GHNOrder, $affId)
    {
        $this->checkConfig();

        $url = $this->endpoint . '/CreateOrder';
        $data = $GHNOrder->buildCreateOrderData($this->config, $affId);

        $jsonRet = $this->requestApi($url, $data, true);
        $parser = new ApiParserService();
        $parser->parse($jsonRet);

        return $parser;
    }

    /**
     * @param GHNOrder $GHNOrder
     * @return ApiParserService
     * @throws GHNException
     */
    public function updateGHNOrder(GHNOrder $GHNOrder)
    {
        $this->checkConfig();

        $url = $this->endpoint . '/UpdateOrder';
        $data = $GHNOrder->buildUpdateOrderData($this->config);

        $jsonRet = $this->requestApi($url, $data, true);
        $parser = new ApiParserService();
        $parser->parse($jsonRet);

        return $parser;
    }

    /**
     * @param GHNOrder $GHNOrder
     * @return ApiParserService
     * @throws GHNException
     */
    public function cancelOrder(GHNOrder $GHNOrder)
    {
        $this->checkConfig();

        $url = $this->endpoint . '/CancelOrder';
        $data = $GHNOrder->buildCancelData($this->config);

        $jsonRet = $this->requestApi($url, $data, true);
        $parser = new ApiParserService();
        $parser->parse($jsonRet);

        return $parser;
    }

    /**
     * API request processing
     *
     * @param $url
     * @param array $data
     * @param bool $post
     * @return mixed|string
     */
    public function requestApi($url, $data = [], $post = false)
    {
        log_info('GHN plugin call API: ' . $url);
        if ($post === false && count($data) > 0) {
            $url .= '?'.http_build_query($data);
        }

        $curl = curl_init($url);

        if ($post) {
            curl_setopt($curl, CURLOPT_POST, 1);

            if (count($data) > 0) {
                curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
            }
        }

        $baseUrl = null;
        if ($this->requestStack->getCurrentRequest()) {
            $baseUrl = $this->requestStack->getCurrentRequest()->getSchemeAndHttpHost().$this->requestStack->getCurrentRequest()->getBasePath();
        }

        // Option array
        $options = [
            // HEADER
            CURLOPT_HTTPHEADER => [
                'X-ECCUBE-URL: '.$baseUrl,
                'X-ECCUBE-VERSION: '.Constant::VERSION,
                // ghn require
                'Accept: application/json',
                'Content-Type: application/json',
            ],
            CURLOPT_HTTPGET => $post === false,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FAILONERROR => false,
            CURLOPT_CAINFO => \Composer\CaBundle\CaBundle::getSystemCaRootBundlePath(),
            CURLOPT_TIMEOUT_MS => 10000,
        ];

        // Set option value
        curl_setopt_array($curl, $options);
        $result = curl_exec($curl);
        $info = curl_getinfo($curl);
        $message = curl_error($curl);
        $info['message'] = $message;
        curl_close($curl);
        log_info('GHN plugin info: ', $info);

//        if ($info['http_code'] !== 200) {
//            throw new NotFoundResourceException();
//        }

        return $result ? $result : $message;
    }

    /**
     * @throws GHNException
     */
    private function checkConfig()
    {
        $config = $this->config;
        if (!$config) {
            throw new GHNException('ghn.api.call.not_config');
        }
    }
}
