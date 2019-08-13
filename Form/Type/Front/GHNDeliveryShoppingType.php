<?php
/**
 * Author: lqdung1992@gmail.com
 * Date: 1/31/2019
 * Time: 2:56 PM
 */

namespace Plugin\OSGHNDelivery\Form\Type\Front;

use Doctrine\ORM\EntityRepository;
use Eccube\Entity\Shipping;
use Plugin\OSGHNDelivery\Entity\GHNPref;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class GHNDeliveryShoppingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** Master Pref ID of eccube $Pref */
        $Pref = $options['Pref'];

        $builder
            ->add('gHNPref', EntityType::class, [
                'label' => 'ghn.shopping.delivery.district',
                'class' => GHNPref::class,
                'choice_label' => 'district_name',
                'query_builder' => function (EntityRepository $entityRepository) use ($Pref) {
                    return $entityRepository->createQueryBuilder('ghn_pref')
                        ->where('ghn_pref.Pref = :pref')
                        ->setParameter('pref', $Pref)
                        ->orderBy('ghn_pref.district_name', ' DESC');
                },
                'placeholder' => '----------------'
            ])
            ->add('main_service_id', TextType::class, [
                'mapped' => false,
                'constraints' => [
                    new NotBlank(['message' => trans('ghn.shopping.delivery.service_incorrect')]),
                    new Regex(['pattern' => "/^\d+$/u", 'message' => trans('ghn.shopping.delivery.service_incorrect')]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired('Pref');
        $resolver->setDefaults(['data_class' => Shipping::class, 'allow_extra_fields' => true]);
    }

    public function getBlockPrefix()
    {
        return parent::getBlockPrefix();
    }
}
