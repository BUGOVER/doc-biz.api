<?php
declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\GroupUser;
use App\Repositories\Contracts\GroupUserRepositoryInterface;

/**
 * Class GroupUserRepositoryInterface
 * @package App\Repositories\Eloquent
 */
class GroupUserRepository extends BaseRepository implements GroupUserRepositoryInterface
{
    /**
     * @return mixed|string
     */
    public function modelClass()
    {
        return GroupUser::class;
    }
}
