<?php
/**
 * Author: lqdung1992@gmail.com
 * Date: 2/21/2019
 * Time: 11:15 AM
 */

namespace Plugin\OSGHNDelivery\Form\Extension;


use Eccube\Entity\Delivery;
use Eccube\Form\Type\Admin\ShippingType;
use Plugin\OSGHNDelivery\Entity\GHNPref;
use Plugin\OSGHNDelivery\Entity\GHNService;
use Plugin\OSGHNDelivery\Repository\GHNDeliveryRepository;
use Plugin\OSGHNDelivery\Repository\GHNServiceRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class ShippingExtension extends AbstractTypeExtension
{
    /** @var GHNDeliveryRepository */
    private $GHNDeliveryRepository;

    /** @var GHNServiceRepository */
    private $serviceRepo;

    /**
     * ShippingExtension constructor.
     * @param GHNDeliveryRepository $deliveryRepo
     * @param GHNServiceRepository $serviceRepo
     */
    public function __construct(GHNDeliveryRepository $deliveryRepo, GHNServiceRepository $serviceRepo)
    {
        $this->GHNDeliveryRepository = $deliveryRepo;
        $this->serviceRepo = $serviceRepo;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $deliveryRepo = $this->GHNDeliveryRepository;
        $serviceRepo = $this->serviceRepo;
        $builder->add('GHNPref', EntityType::class, [
            'label' => 'ghn.warehouse.district',
            'required' => false,
            'class' => GHNPref::class,
            'help' => '',
            'choice_label' => function(?GHNPref $GHNPref) {
                return $GHNPref->getProvinceName() . ' - ' . $GHNPref->getDistrictName();
            },
            'placeholder' => '----------------',
            'eccube_form_options' => [
                'auto_render' => true,
                'form_theme' => '@OSGHNDelivery\admin\form_shipping_district.twig',
            ]
        ])
            ->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) use ($deliveryRepo) {
                $form = $event->getForm();
                /** @var Delivery $delivery */
                $delivery = $form['Delivery']->getData();
                if (!$delivery) {
                    return;
                }
                $pref = $form['GHNPref']->getData();
                $ghnDelivery = $deliveryRepo->find($delivery);
                if ($ghnDelivery) {
                    if (empty($pref)) {
                        $form['GHNPref']->addError(new FormError(trans('This value should not be blank.', [], 'validators')));
                        return;
                    }

                    $service = $form['main_service_id']->getData();
                    if (empty($service)) {
                        $form['GHNPref']->addError(new FormError(trans('ghn.shopping.delivery.service_incorrect')));
                    }
                }
            })
            // set default value for service
            ->addEventListener(FormEvents::POST_SET_DATA, function (FormEvent $event) use ($serviceRepo) {
                $Shipping = $event->getData();
                $form = $event->getForm();
                $form->add('main_service_id', TextType::class, [
                    'mapped' => false,
                    'required' => false
                ]);

                if (!$Shipping) {
                    return;
                }

                /** @var GHNService $service */
                $service = $serviceRepo->findOneBy(['Shipping' => $Shipping]);
                $form['main_service_id']->setData($service ? $service->getMainServiceId() : null);

            });
    }


    public function getExtendedType()
    {
        return ShippingType::class;
    }

}