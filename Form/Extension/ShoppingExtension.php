<?php
/**
 * Author: lqdung1992@gmail.com
 * Date: 3/1/2019
 * Time: 3:40 PM
 */

namespace Plugin\OSGHNDelivery\Form\Extension;


use Eccube\Entity\Delivery;
use Eccube\Entity\Shipping;
use Eccube\Form\Type\Shopping\ShippingType;
use Plugin\OSGHNDelivery\Entity\GHNDelivery;
use Plugin\OSGHNDelivery\Repository\GHNDeliveryRepository;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Class ShoppingExtension
 * @package Plugin\OSGHNDelivery\Form\Extension
 */
class ShoppingExtension extends AbstractTypeExtension
{
    /** @var GHNDeliveryRepository */
    private $GHNDeliveryRepository;

    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * ShoppingExtension constructor.
     * @param GHNDeliveryRepository $GHNDeliveryRepository
     * @param RequestStack $requestStack
     */
    public function __construct(GHNDeliveryRepository $GHNDeliveryRepository, RequestStack $requestStack)
    {
        $this->GHNDeliveryRepository = $GHNDeliveryRepository;
        $this->requestStack = $requestStack;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $filter = ['shopping_confirm', 'shopping_checkout'];
        $currentRoute = $this->requestStack->getCurrentRequest()->get('_route');
        if (!in_array($currentRoute, $filter)) {
            return;
        }
        $deliveryRepo = $this->GHNDeliveryRepository;
        $builder
            ->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) use ($deliveryRepo) {
                $form = $event->getForm();
                /** @var Delivery $delivery */
                $delivery = $form['Delivery']->getData();
                if (!$delivery) {
                    return;
                }
                /** @var GHNDelivery $ghnDelivery */
                $ghnDelivery = $deliveryRepo->find($delivery);
                if ($ghnDelivery) {
                    // check must all GHN delivery
                    /** @var Shipping $Shipping */
                    $Shipping = $event->getData();
                    foreach ($Shipping->getOrder()->getShippings() as $shp) {
                        if ($shp->getDelivery()->getId() != $ghnDelivery->getDelivery()->getId()) {
                            $form['Delivery']->addError(new FormError(trans('ghn.shopping.delivery.difference_method')));
                            return;
                        }
                    }
                }
            });
    }

    public function getExtendedType()
    {
        return ShippingType::class;
    }
}
