<?php
declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;

/**
 * Class UserRepository
 * @package App\Repositories\Eloquent
 */
class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    /**
     * @return mixed|string
     */
    public function modelClass()
    {
        return User::class;
    }
}
