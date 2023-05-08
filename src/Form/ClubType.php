<?php

namespace App\Form;

use App\Entity\Club;
use PhpParser\Node\Expr\BinaryOp\NotEqual;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\IsNull;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotEqualTo;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\PositiveOrZero;
use Symfony\Component\Validator\Constraints\Range;
use Symfony\Component\Validator\Constraints\Unique;

class ClubType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', null, [
                'constraints' => [
                    new NotBlank(),
                    new Length(["min" => 3, 'minMessage'=> 'Name should contain more than 3 letters']),
                ],

            ])
            ->add('email', null, [
                'constraints' => [
                    new NotBlank(),
                    new Email(['message' => 'The email is not a valid email.']),

                ],
            ])
            ->add('budget', null, [
                'constraints' => [
                    new NotBlank(),
                    new PositiveOrZero(),
                    new Range([
                        'min' => 25000,
                        'max' => 30000,
                        'notInRangeMessage' => 'The initial budget should be between 25000 and 30000'
                    ])
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
            'data_class' => Club::class,
        ]);
    }

    public function getBlockPrefix()
    {
        return '';
    }
}
