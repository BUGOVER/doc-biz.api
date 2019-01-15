<?php
declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\CompanyUser;
use App\Repositories\Contracts\CompanyUserRepositoryInterface;

/**
 * Class CompanyUserRepository
 * @package App\Repositories\Eloquent
 */
class CompanyUserRepository extends BaseRepository implements CompanyUserRepositoryInterface
{
    /**
     * @return mixed|string
     */
    public function modelClass()
    {
        return CompanyUser::class;
    }
}
