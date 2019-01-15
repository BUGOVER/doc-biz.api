<?php
declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\Group;
use App\Repositories\Contracts\GroupRepositoryInterface;

/**
 * Class GroupRepository
 * @package App\Repositories\Eloquent
 */
class GroupRepository extends BaseRepository implements GroupRepositoryInterface
{
    /**
     * @return mixed|string
     */
    public function modelClass()
    {
        return Group::class;
    }
}
