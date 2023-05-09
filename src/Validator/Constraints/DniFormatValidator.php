<?php

namespace App\Validator\Constraints;

use App\Validator\App;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class DniFormatValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        /* @var App\Validator\DniFormat $constraint */

        if (!\preg_match('/^[0-9]{8}[A-Za-z]$/', $value)) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ value }}', $value)
                ->addViolation();
        }
    }
}
