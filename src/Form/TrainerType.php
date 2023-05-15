<?php

namespace App\Form;

use App\Entity\Trainer;
use App\Validator\Constraints\DniFormat;
use App\Validator\Constraints\Salary;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\PositiveOrZero;
use Symfony\Component\Validator\Constraints\Range;

class TrainerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', null, [
                'constraints' => [
                    new NotBlank(),
                    new Length(["min" => 3, 'minMessage' => 'The name should contain more than 3 letters']),],
            ])
            ->add('surname', null, [
                'constraints' => [
                    new NotBlank(),
                    new Length(["min" => 3, 'minMessage' => 'The surname should contain more than 3 letters']),],
            ])
            ->add('email', null, [
                'constraints' => [
                    new NotBlank(),
                    new Email(['message' => 'The email is not a valid email.']),

                ],
            ])
            ->add('dni', null, [
                'constraints' => [
                    new NotBlank(),
                    new DniFormat()
                ],
            ])
            ->add('salary', null, [
                'constraints' => [
                    new NotBlank(),
                    new PositiveOrZero(),
                    new Range([
                        'min' => 2000,
                        'max' => 3000,
                        'notInRangeMessage' => 'The salary should be between {{ min }} and {{ max }}'
                    ]),
                    new Salary($options["club"]),
                ],
            ])
            ->add('phone', null, [
                'constraints' => [
                    new NotBlank(),
                    new Length(['min' => 6,
                        'max' => 9,
                        'minMessage' => 'Not a real number, too short',
                        'maxMessage' => 'Not a real number, too long'
                    ]),


                ]
            ])// Add more fields as needed

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Trainer::class,
            'club' => null,
        ]);
    }

    public function getBlockPrefix()
    {
        return '';
    }
}
