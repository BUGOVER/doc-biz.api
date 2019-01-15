<?php
declare(strict_types=1);

namespace App\Validators;

use LaraValidation\CoreValidator;
use LaraValidation\LaraValidator;

/**
 * Class UserValidator
 * @package App\Validators
 */
class UserValidator extends LaraValidator
{
    /**
     * @return \LaraValidation\CoreValidator
     */
    public function validationDefault(): CoreValidator
    {
        $this->validator
            ->required([
                'user_name',
                'email',
                'password'
            ]);

        return $this->validator;
    }

    /**
     * @return CoreValidator
     */
    public function validationUserSignData(): CoreValidator
    {
        $this->validator->required([
            'email',
            'password',
            'companyKey',
            'companyName',
            'companyId'
        ])->email('email');

        return $this->validator;
    }

    /**
     * @return CoreValidator
     */
    public function validationInviteUser()
    {
        $this->validator->required([
            'email'
        ])->email('email');

        return $this->validator;
    }
}
