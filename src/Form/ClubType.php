<?php

namespace App\Form;

use App\Entity\Club;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class ClubType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', null, [
                'constraints' => [
                    new NotBlank(),
                ],
            ])
            ->add('email', null, [
                'constraints' => [
                    new NotBlank(),
                ],
            ])
            ->add('budget', null, [
                'constraints' => [
                    new NotBlank(),
                ],
            ])
            ->add('phone', null, [
                'constraints' => [
                    new NotBlank(),
                ]
            ])// Add more fields as needed

        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Club::class,
            'csrf_protection' => false,
        ]);
    }
}

