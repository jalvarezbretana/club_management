<?php

namespace App\Validator\Constraints;

use App\Entity\Club;
use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 *
 * @Target({"PROPERTY", "METHOD", "ANNOTATION"})
 */
#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class Salary extends Constraint
{
    /*
     * Any public properties become valid options for the annotation.
     * Then, use these in your validator class.
     */
    public $message = 'The salary ({{ salary }}) is higher than the club budget ({{ budget }}).';

    public function __construct(private ?Club $club = null)
    {
        parent::__construct();
    }

    /**
     * @return Club
     */
    public function getClub(): ?Club
    {
        return $this->club;
    }

    public function validatedBy(): string
    {
        return SalaryValidator::class;
    }
}
