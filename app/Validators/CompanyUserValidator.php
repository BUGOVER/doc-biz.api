<?php
declare(strict_types=1);

namespace App\Validators;

use LaraValidation\CoreValidator;
use LaraValidation\LaraValidator;

/**
 * Class CompanyUserValidator
 * @package App\Validators
 */
class CompanyUserValidator extends LaraValidator
{
    /**
     * @return CoreValidator
     */
    public function validationDeleteUser(): CoreValidator
    {
        $this->validator->required('usersId');

        return $this->validator;
    }
}
