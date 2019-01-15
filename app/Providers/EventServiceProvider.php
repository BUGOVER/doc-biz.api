<?php
declare(strict_types=1);

namespace App\Providers;

use App\Events\Broadcast\DocumentAdded;
use App\Events\InvitationConfirmed;
use App\Events\InvitationCreated;
use App\Listeners\SendInvitationEmail;
use App\Listeners\SendWelcomeEmail;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

/**
 * Class EventServiceProvider
 *
 * @package App\Providers
 */
class EventServiceProvider extends ServiceProvider
{
    /**
     * @var array
     */
    protected $listen = [
        InvitationCreated::class => [
            SendInvitationEmail::class
        ],
        DocumentAdded::class => [

        ],
        InvitationConfirmed::class => [
            SendWelcomeEmail::class
        ]
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();
    }
}
