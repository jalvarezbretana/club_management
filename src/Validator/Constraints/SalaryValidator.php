<?php

namespace App\Validator\Constraints;


use App\Entity\Club;
use App\Validator\App;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class SalaryValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        /* @var App\Validator\Salary $constraint */
        /** @var FormInterface $form */

        $form = $this->context->getObject();

        /** @var Club $club */

        $club = $constraint->getClub();
        $salary = $form->getData();

        if (/*$club &&*/ (($club->getBudget() - $salary) < 0)) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ salary }}', $salary)
                ->setParameter('{{ budget }}', $club->getBudget())
                ->addViolation();
        }
    }
}
