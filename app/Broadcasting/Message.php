<?php
declare(strict_types=1);

namespace App\Broadcasting;

use App\Models\User;

/**
 * Class Message
 * @package App\Broadcasting
 */
class Message
{
    /**
     * Create a new channel instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Authenticate the user's access to the channel.
     *
     * @param  \App\Models\User $user
     * @return array|bool
     */
    public function join(User $user)
    {
        //
    }
}
