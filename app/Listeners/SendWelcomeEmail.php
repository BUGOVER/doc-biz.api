<?php
declare(strict_types=1);

namespace App\Listeners;

use App\Mail\InvitationWelcome;

/**
 * Class SendWelcomeEmail
 * @package App\Listeners
 */
class SendWelcomeEmail
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * @param $event
     * @return bool
     */
    public function handle($event)
    {
        \Mail::to($event->email)->queue(new InvitationWelcome($event->userName, $event->companyName));

        return !(count(\Mail::failures()) > 0);
    }
}
