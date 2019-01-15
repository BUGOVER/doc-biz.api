<?php
declare(strict_types=1);

namespace App\Jobs;

use App\Events\InvitationCreated;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;

/**
 * Class CreateInvitation
 * @package App\Jobs
 */
class CreateInvitation implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var
     */
    protected $data;

    /**
     * CreateInvitation constructor.
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * @return array|bool
     */
    public function handle()
    {
        $key = $this->data['token'] = generate_token();

        Cache::put('user_invitation_data_' . $key, json_encode($this->data, JSON_UNESCAPED_UNICODE),
            Carbon::now()->addHour(config('app_config.invitation_link_period')));

        if (event(new InvitationCreated($key))) {
            return $key;
        }

        return false;
    }
}
