<?php
declare(strict_types=1);

namespace App\Validators;

use LaraValidation\CoreValidator;
use LaraValidation\LaraValidator;

/**
 * Class GroupValidator
 * @package App\Validators
 */
class GroupValidator extends LaraValidator
{
    /**
     * @return \LaraValidation\CoreValidator
     */
    public function validationAddGroup(): CoreValidator
    {
        $this->validator->required([
            'name',
            'roleType'
        ])->maxLength('name', 50);

        return $this->validator;
    }
}
