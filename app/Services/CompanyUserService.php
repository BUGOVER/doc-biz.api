<?php
declare(strict_types=1);

namespace App\Services;

use App\Repositories\Contracts\CompanyUserRepositoryInterface as CompanyUserRepository;
use App\Validators\CompanyUserValidator;

/**
 * Class CompanyUserService
 * @package App\Services
 */
class CompanyUserService extends BaseService
{
    /**
     * CompanyUserService constructor.
     * @param CompanyUserRepository $companyUserRepository
     * @param CompanyUserValidator $companyUserValidator
     */
    public function __construct(
        CompanyUserRepository $companyUserRepository,
        CompanyUserValidator $companyUserValidator
    ) {
        $this->baseRepository = $companyUserRepository;
        $this->baseValidator = $companyUserValidator;
    }
}
