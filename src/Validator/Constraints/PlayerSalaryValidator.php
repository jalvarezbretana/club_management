<?php

namespace App\Validator\Constraints;


use App\Entity\Club;
use App\Validator\App;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class PlayerSalaryValidator extends ConstraintValidator
{

    public function validate($value, Constraint $constraint)
    {
        /* @var App\Validator\PlayerSalary $constraint */
        /** @var FormInterface $form */

        $form = $this->context->getObject();

        /** @var Club $club */

        $club = $constraint->getClub();
        $playerSalary = $form->getData();

        if (($club->getBudget() - $playerSalary) < 0) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ salary }}', $playerSalary)
                ->setParameter('{{ budget }}', $club->getBudget())
                ->addViolation();
        }
    }
}
