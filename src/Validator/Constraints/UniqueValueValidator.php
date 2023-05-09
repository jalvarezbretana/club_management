<?php

namespace App\Validator\Constraints;

use App\Repository\ClubRepository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class UniqueValueValidator extends ConstraintValidator
{
    private $clubRepository;

    public function __construct(ClubRepository $clubRepository)
    {
        $this->clubRepository = $clubRepository;

    }

    public function validate($value, Constraint $constraint)
    {
        /* @var $constraint /App\Validator\UniqueValue  */
        $existingValue = $this->clubRepository->findOneBy([
            'name' => $value,
            'email' => $value,
            'phone' => $value
        ]);

        if (!$existingValue) {
            return;
        }

        // TODO: implement the validation here
        $this->context->buildViolation($constraint->message)
//            ->setParameter('{{ value }}', $value)
            ->addViolation();
    }
}
