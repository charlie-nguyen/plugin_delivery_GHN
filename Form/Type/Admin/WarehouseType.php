<?php

namespace Plugin\GHNDelivery\Form\Type\Admin;

use Eccube\Common\EccubeConfig;
use Plugin\GHNDelivery\Entity\GHNPref;
use Plugin\GHNDelivery\Entity\GHNWarehouse;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class WarehouseType extends AbstractType
{
    /** @var EccubeConfig */
    protected $eccubeConfig;

    /**
     * WarehouseType constructor.
     * @param EccubeConfig $eccubeConfig
     */
    public function __construct(EccubeConfig $eccubeConfig)
    {
        $this->eccubeConfig = $eccubeConfig;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('address', TextType::class, [
                'label' => 'ghn.warehouse.address',
                'required' => true,
                'constraints' => [
                    new NotBlank(),
                    new Length(['max' => $this->eccubeConfig->get('eccube_smtext_len')])
                ]
            ])
            ->add('contact_name', TextType::class, [
                'label' => 'ghn.warehouse.contact_name',
                'required' => true,
                'constraints' => [
                    new NotBlank(),
                    new Length(['max' => $this->eccubeConfig->get('eccube_smtext_len')])
                ]
            ])
            ->add('contact_phone', TextType::class, [
                'label' => 'ghn.warehouse.contact_phone',
                'required' => true,
                'constraints' => [
                    new NotBlank(),
                    new Length(['max' => $this->eccubeConfig->get('eccube_tel_len_max')])
                ]
            ])

            ->add('GHNPref', EntityType::class, [
                'label' => 'ghn.warehouse.district',
                'class' => GHNPref::class,
                'choice_label' => function(?GHNPref $GHNPref) {
                    return $GHNPref->getProvinceName() . ' - ' . $GHNPref->getDistrictName();
                },
//                'query_builder' => function (EntityRepository $entityRepository) {
//                    return $entityRepository->createQueryBuilder('ghn_pref');
//                },
            ])
            ->add('email', EmailType::class, [
                'label' => 'common.mail_address',
                'required' => false
            ])
//            ->add('is_main', ChoiceType::class, [
//                'label_attr' => ['class' => 'col-form-label'],
//                'label' => 'ghn.warehouse.is_main',
//                'expanded' => true,
//                'multiple' => false,
//                'choices' => [
//                    'ghn.warehouse.is_main.yes' => true,
//                    'ghn.warehouse.is_main.no' => false,
//                ],
//            ])
            ->add('lati', NumberType::class, [
                'label' => 'ghn.warehouse.lati',
                'required' => false,
                'constraints' => []
            ])
            ->add('long', NumberType::class, [
                'label' => 'ghn.warehouse.long',
                'required' => false,
                'constraints' => []
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => GHNWarehouse::class,
        ]);
    }
}
