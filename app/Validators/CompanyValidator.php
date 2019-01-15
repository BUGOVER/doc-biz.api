<?php
declare(strict_types=1);

namespace App\Validators;

use App\Config\ConstPersonPermission;
use App\Config\ConstPersonRole;
use LaraValidation\CoreValidator;
use LaraValidation\LaraValidator;

/**
 * Class CompanyValidator
 * @package App\Validators
 */
class CompanyValidator extends LaraValidator
{
    /**
     * @return CoreValidator
     */
    public function validationSendEmail(): CoreValidator
    {
        $this->validator->required([
            'email',
            'expire'
        ])->email('email');

        return $this->validator;
    }

    /**
     * @return CoreValidator
     */
    public function validationCreateUserWithCompany(): CoreValidator
    {
        $this->validator
            ->required(['company_name', 'user_name', 'password', 'email'])
            ->add('company_name', 'required|unique:companies,name')
            ->add('user_name', 'required:users,name')
            ->add('password', 'required:users,password')
            ->inClassConstant('role', ConstPersonRole::class)
            ->inClassConstant('permission', ConstPersonPermission::class);

        return $this->validator;
    }

    /**
     * @return CoreValidator
     */
    public function validationCheckCompanyName(): CoreValidator
    {
        $this->validator->required('company_name');

        return $this->validator;
    }
}
