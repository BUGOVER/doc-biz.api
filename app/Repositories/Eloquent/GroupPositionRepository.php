<?php
declare(strict_types=1);


namespace App\Repositories\Eloquent;


use App\Models\GroupPosition;
use App\Repositories\Contracts\GroupPositionRepositoryInterface;

/**
 * Class GroupPositionRepository
 * @package App\Repositories\Eloquent
 */
class GroupPositionRepository extends BaseRepository implements GroupPositionRepositoryInterface
{
    /**
     * Specify Model class name
     *
     * @return mixed
     */
    public function modelClass()
    {
        return GroupPosition::class;
    }
}
