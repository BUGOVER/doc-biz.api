<?php
declare(strict_types=1);

namespace App\Validators;

use LaraValidation\CoreValidator;
use LaraValidation\LaraValidator;

/**
 * Class DocumentValidator
 * @package App\Validators
 */
class DocumentValidator extends LaraValidator
{
    /**
     * @return \LaraValidation\CoreValidator
     */
    public function validationAddDocument(): CoreValidator
    {
        $this->validator->
        required([
            'name',
            'description',
            'data'
        ])->maxLength('data', 65000)
            ->maxLength('description', 500)
            ->maxLength('name', 50);

        return $this->validator;
    }
}
