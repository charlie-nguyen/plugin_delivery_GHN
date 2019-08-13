<?php
/**
 * Author: lqdung1992@gmail.com
 * Date: 1/29/2019
 * Time: 1:38 PM
 */

namespace Plugin\OSGHNDelivery\Controller\Front;

use Eccube\Controller\AbstractController;
use Plugin\OSGHNDelivery\Repository\GHNOrderCallbackRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CallbackController extends AbstractController
{
    /**
     * @var GHNOrderCallbackRepository
     */
    protected $callbackRepo;

    /**
     * CallbackController constructor.
     * @param GHNOrderCallbackRepository $callbackRepo
     */
    public function __construct(GHNOrderCallbackRepository $callbackRepo)
    {
        $this->callbackRepo = $callbackRepo;
    }

    /**
     * @param Request $request
     * @Route(name="ghn_callback", path="/receive")
     */
    public function index(Request $request)
    {
        log_info("Callback from GHN start");
        $json = $request->getContent();
        $this->callbackRepo->saveCallbackData($json, $request->headers->all());

        log_info("Callback from GHN end");

        return new Response(trans('ghn.callback.receive'));
    }
}