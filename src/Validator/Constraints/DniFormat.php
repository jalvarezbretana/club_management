<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 *
 * @Target({"PROPERTY", "METHOD", "ANNOTATION"})
 */
#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class DniFormat extends Constraint
{
    /*
    * Any public properties become valid options for the annotation.
    * Then, use these in your validator class.
    */
    public $message = 'The DNI {{ value }} is not valid. It should have 8 numbers and 1 letter.';

    public function validatedBy(): string
    {
        return \get_class($this) . 'Validator';
    }
}
