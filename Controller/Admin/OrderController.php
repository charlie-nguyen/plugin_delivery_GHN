<?php
/**
 * Author: lqdung1992@gmail.com
 * Date: 2/21/2019
 * Time: 1:48 PM
 */

namespace Plugin\OSGHNDelivery\Controller\Admin;


use Eccube\Controller\AbstractController;
use Eccube\Entity\Master\OrderStatus;
use Eccube\Entity\Order;
use Eccube\Entity\Shipping;
use Plugin\OSGHNDelivery\Entity\GHNConfig;
use Plugin\OSGHNDelivery\Entity\GHNPref;
use Plugin\OSGHNDelivery\Entity\GHNWarehouse;
use Plugin\OSGHNDelivery\Repository\GHNConfigRepository;
use Plugin\OSGHNDelivery\Repository\GHNDeliveryRepository;
use Plugin\OSGHNDelivery\Repository\GHNOrderRepository;
use Plugin\OSGHNDelivery\Repository\GHNPrefRepository;
use Plugin\OSGHNDelivery\Repository\GHNServiceRepository;
use Plugin\OSGHNDelivery\Repository\GHNWarehouseRepository;
use Plugin\OSGHNDelivery\Service\ApiService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class OrderController
 * @package Plugin\OSGHNDelivery\Controller\Admin
 */
class OrderController extends AbstractController
{
    /**
     * @var GHNWarehouseRepository
     */
    protected $warehouseRepo;

    /**
     * @var ApiService
     */
    protected $apiService;

    /**
     * @var GHNServiceRepository
     */
    protected $serviceRepo;

    /**
     * @var GHNConfigRepository
     */
    protected $configRepo;

    /** @var GHNPrefRepository */
    protected $prefRepo;

    /** @var GHNDeliveryRepository */
    protected $ghnDeliveryRepo;

    /** @var GHNOrderRepository */
    protected $ghnOrderRepo;

    /**
     * OrderController constructor.
     * @param GHNWarehouseRepository $warehouseRepo
     * @param ApiService $apiService
     * @param GHNServiceRepository $serviceRepo
     * @param GHNConfigRepository $configRepo
     * @param GHNPrefRepository $prefRepo
     * @param GHNDeliveryRepository $ghnDeliveryRepo
     * @param GHNOrderRepository $ghnOrderRepo
     */
    public function __construct(GHNWarehouseRepository $warehouseRepo, ApiService $apiService, GHNServiceRepository $serviceRepo, GHNConfigRepository $configRepo, GHNPrefRepository $prefRepo, GHNDeliveryRepository $ghnDeliveryRepo, GHNOrderRepository $ghnOrderRepo)
    {
        $this->warehouseRepo = $warehouseRepo;
        $this->apiService = $apiService;
        $this->serviceRepo = $serviceRepo;
        $this->configRepo = $configRepo;
        $this->prefRepo = $prefRepo;
        $this->ghnDeliveryRepo = $ghnDeliveryRepo;
        $this->ghnOrderRepo = $ghnOrderRepo;
    }


    /**
     * ajax
     *
     * @param Request $request
     * @param int $id to district
     * @return array
     *
     * @Route(path="/%eccube_admin_route%/ghn/service/{id}", name="ghn_order_service_fee", requirements={"id"="\d+"}, methods={"POST"})
     * @Route(path="/%eccube_admin_route%/ghn/service/{id}/{index}", name="ghn_order_service_fee_index", requirements={"id"="\d+"}, methods={"POST"})
     * @Route(path="/%eccube_admin_route%/ghn/service", name="ghn_order_service_fee_no_id", methods={"POST"})
     * @Template("@OSGHNDelivery/admin/order_service_ajax.twig")
     */
    public function getServiceAndFee(Request $request, $id, $index = null)
    {
        if(!$request->isXmlHttpRequest()) {
            throw new NotFoundHttpException();
        }

        $this->isTokenValid();
        if (!is_numeric($id)) {
            throw new NotFoundHttpException();
        }

        /** @var GHNPref $GHNPref */
        $GHNPref = $this->prefRepo->find($id);
        if (!$GHNPref) {
            throw new NotFoundHttpException();
        }

        $data = [];
        $message = null;

        // from main warehouse
        /** @var GHNWarehouse $warehouse */
        $warehouse = $this->warehouseRepo->getOne();
        $fromDistrict = (int) $warehouse->getGHNPref()->getDistrictId();
        $toDistrict = (int) $GHNPref->getDistrictId();

        $isNeedCallApi = true;
        $sessionName = $this->eccubeConfig->get('admin_ghn_session_service_fee') . $index;
        if ($request->getSession()->has($sessionName)) {
            $isNeedCallApi = false;
            $data = $request->getSession()->get($sessionName);

            if ($fromDistrict != $data['from_id'] || $toDistrict != $data['to_id']) {
                $isNeedCallApi = true;
            }
        }

        if ($isNeedCallApi) {
            $weight = $this->configRepo->find(1)->getWeight();

            // call api to get service list
            $output = $this->apiService->findAvailableServices($fromDistrict, $toDistrict, ['Weight' => $weight]);
            $data = $output->getData();
            $message = $output->getMsg();
        }

        $data['from_id'] = $fromDistrict;
        $data['to_id'] = $toDistrict;

        $request->getSession()->set($sessionName, $data);

        return ['ghn_service' => $data, 'message' => $message, 'index' => $index];
    }


    /**
     * @param Request $request
     * @param Order $Order
     * @Route(path="/%eccube_admin_route%/ghn/reorder/{id}", name="ghn_reorder", requirements={"id"="\d+"})
     * @ParamConverter("Order")
     */
    public function reRegisterOrder(Request $request, Order $Order)
    {
        $url = $this->redirectToRoute('admin_order_edit', ['id' => $Order->getId()]);

        /** @var GHNConfig $config */
        $config = $this->configRepo->find(1);
        if (!$config) {
            $this->addError('ghn.config.missing', 'admin');
            return $url;
        }

        if (!$Order->getId() || $Order->getOrderStatus()->getId() != OrderStatus::IN_PROGRESS) {
            $this->addError('ghn.order.reorder.can_not_create', 'admin');

            return $url;
        }

        /** @var Shipping $shipping */
        foreach ($Order->getShippings() as $shipping) {
            $this->ghnOrderRepo->createGHNOrderByShipping($shipping, false);
        }
        // please flush all for update order
        $this->entityManager->flush();

        return $url;
    }
}