<?php

namespace FluffyRollBundle\Form;

use FluffyRollBundle\Entity\Teddy;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TeddyType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $fileRequired = isset($options['fileRequired']) ? $options['fileRequired'] : true;

        $builder
            ->add(
                'name',
                TextType::class,
                [
                    'attr' => [
                        'class' => 'form-control',
                    ],
                ]
            )
            ->add(
                'imageFile',
                FileType::class,
                [
                    'required' => $fileRequired,
                    'attr' => [
                        'accept' => 'image/*',
                        'capture' => 'camera',
                        'class' => 'form-control',
                    ],
                ]
            );
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => Teddy::class,
                'fileRequired' => true
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'fluffyrollbundle_teddy';
    }


}
