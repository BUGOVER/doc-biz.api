<?php
declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\UserInvitation;
use App\Repositories\Contracts\UserInvitationRepositoryInterface;

/**
 * Class UserInvitationRepositoryInterface
 * @package App\Repositories\Eloquent
 */
class UserInvitationRepository extends BaseRepository implements UserInvitationRepositoryInterface
{
    /**
     * @return mixed|string
     */
    public function modelClass()
    {
        return UserInvitation::class;
    }
}
