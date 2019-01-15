<?php
declare(strict_types=1);

namespace App\Validators;

use LaraValidation\CoreValidator;
use LaraValidation\LaraValidator;

/**
 * Class UserInvitationValidator
 * @package App\Validators
 */
class UserInvitationValidator extends LaraValidator
{
    /**
     * @return \LaraValidation\CoreValidator
     */
    public function validationDefault(): CoreValidator
    {
        $this->validator->required([
            'name',
            'email'
        ])
            ->maxLength('name', 100)
            ->maxLength('email', 255);

        return $this->validator;
    }

    /**
     * @return \LaraValidation\CoreValidator
     */
    public function validationInviteCreate(): CoreValidator
    {
        $this->validator
            ->required(['name', 'email', 'password', 'token'])
            ->email('email');

        return $this->validator;
    }
}
