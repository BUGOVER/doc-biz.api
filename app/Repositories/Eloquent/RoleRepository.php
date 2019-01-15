<?php
declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\Role;
use App\Repositories\Contracts\RoleRepositoryInterface;

/**
 * Interface RoleRepository
 * @package App\Repositories\Eloquent
 */
class RoleRepository extends BaseRepository implements RoleRepositoryInterface
{
    /**
     * Specify Model class name
     *
     * @return mixed
     */
    public function modelClass()
    {
        return Role::class;
    }
}
