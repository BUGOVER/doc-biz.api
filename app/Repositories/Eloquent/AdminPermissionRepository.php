<?php
declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\AdminPermission;
use App\Repositories\Contracts\AdminPermissionRepositoryInterface;

/**
 * Class AdminPermissionRepository
 * @package App\Repositories\Eloquent
 */
class AdminPermissionRepository extends BaseRepository implements AdminPermissionRepositoryInterface
{
    /**
     * @return mixed|string
     */
    public function modelClass()
    {
        return AdminPermission::class;
    }
}
