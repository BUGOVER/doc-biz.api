<?php
declare(strict_types=1);

namespace App\Listeners;

use App\Events\InvitationCreated;
use App\Mail\InvitationEmail;
use App\Repositories\Contracts\CompanyRepositoryInterface as CompanyRepository;
use App\Repositories\Contracts\UserRepositoryInterface as UserRepository;

/**
 * Class SendInvitationEmail
 * @package App\Listeners
 */
class SendInvitationEmail
{
    /**
     * @var UserRepository
     */
    protected $userRepository;

    /**
     * @var CompanyRepository
     */
    protected $companyRepository;

    /**
     * SendInvitationEmail constructor.
     * @param UserRepository $userRepository
     * @param CompanyRepository $companyRepository
     */
    public function __construct(UserRepository $userRepository, CompanyRepository $companyRepository)
    {
        $this->userRepository = $userRepository;
        $this->companyRepository = $companyRepository;
    }

    /**
     * @param InvitationCreated $event
     * @return bool
     */
    public function handle(InvitationCreated $event): bool
    {
        $data = \Cache::get('user_invitation_data_' . $event->inviteKey);
        $emailData = json_decode($data);
        $senderName = $this->userRepository->find($emailData->sender_id, ['name']);
        $companyName = $this->companyRepository->find($emailData->company_id, ['name']);

        $params = [
            'user_name' => $emailData->name,
            'sender_name' => $senderName['name'],
            'company_name' => $companyName['name'],
            'invitation_link' => config('app_config.invitation_accepted_redirection_link') . $event->inviteKey
        ];
        \Mail::to($emailData->email)->queue(new InvitationEmail($params));

        return !(count(\Mail::failures()) > 0);
    }
}
