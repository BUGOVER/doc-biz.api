<?php
declare(strict_types=1);

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Class InvitationConfirmed
 * @package App\Events
 */
class InvitationConfirmed
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var
     */
    public $userName;

    /**
     * @var
     */
    public $companyName;

    /**
     * @var
     */
    public $email;

    /**
     * InvitationConfirmed constructor.
     * @param $userName
     * @param $companyName
     * @param $email
     */
    public function __construct($userName, $companyName, $email)
    {
        $this->userName = $userName;
        $this->companyName = $companyName;
        $this->email = $email;
    }

    /**
     * @return PrivateChannel
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
