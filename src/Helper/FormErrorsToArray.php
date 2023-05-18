<?php

namespace App\Helper;

use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;

class FormErrorsToArray
{
    public static function staticParseErrorsToArray(FormInterface $form): array
    {

        $errors = [];
        if (!$form->isSubmitted()) {
            $form->addError(new FormError("Form not send"));
        }
        // Global
        foreach ($form->getErrors() as $error) {
            $errors[] = $error->getMessage();
        }
        if (!$form->isSubmitted()) {
            return $errors;
        }
        // Fields
        /** @var FormInterface $child */
        foreach ($form as $child) {
            if (!$child->isValid()) {
                foreach ($child->getErrors() as $error) {
                    $errors[] = $child->getName() . " | " . $error->getMessage();
                }
            }
        }
        return $errors;
    }
}