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
    public $message = 'The DNI {{ value }} is not valid. It should have 8 numbers and 1 letter.';

    public function validatedBy()
    {
        return \get_class($this) . 'Validator';
    }
}
