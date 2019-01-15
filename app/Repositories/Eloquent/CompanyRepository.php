<?php
declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\Company;
use App\Repositories\Contracts\CompanyRepositoryInterface;

/**
 * Class CompanyRepository
 * @package App\Repositories\Eloquent
 */
class CompanyRepository extends BaseRepository implements CompanyRepositoryInterface
{
    /**
     * @return mixed|string
     */
    public function modelClass()
    {
        return Company::class;
    }
}
