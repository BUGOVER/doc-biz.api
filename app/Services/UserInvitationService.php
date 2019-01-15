<?php
declare(strict_types=1);

namespace App\Services;

use App\Jobs\ConfirmInvitation;
use App\Jobs\CreateInvitation;
use App\Repositories\Contracts\CompanyUserRepositoryInterface as CompanyUserRepository;
use App\Repositories\Contracts\UserRepositoryInterface as UserRepository;
use App\Validators\UserInvitationValidator;
use Illuminate\Foundation\Bus\DispatchesJobs;
use LaraRepo\Criteria\Where\WhereInCriteria;

/**
 * Class UserInvitationService
 * @package App\Services
 */
class UserInvitationService extends BaseService
{
    use DispatchesJobs;

    /**
     * @var
     */
    protected $companyUserRepository;

    /**
     * UserInvitationService constructor.
     * @param UserRepository $userRepository
     * @param UserInvitationValidator $userInvitationValidator
     * @param CompanyUserRepository $companyUserRepository
     */
    public function __construct(
        UserRepository $userRepository,
        UserInvitationValidator $userInvitationValidator,
        CompanyUserRepository $companyUserRepository
    ) {
        $this->baseRepository = $userRepository;
        $this->companyUserRepository = $companyUserRepository;
        $this->baseValidator = $userInvitationValidator;
    }

    /**
     * @param array $userData
     * @return array|bool
     */
    public function userInvitation(array $userData)
    {
        if (!$this->validate($this->baseValidator, $userData)) {
            return false;
        }

        $userData['sender_id'] = USER_ID;
        $userData['company_id'] = COMPANY_ID;

        $hasUser = $this->baseRepository->findAllBy('email', $userData['email'], 'user_id')->toArray();

        if ($hasUser) {

            $this->companyUserRepository->pushCriteria(new WhereInCriteria('user_id', $hasUser));
            $hasUserInCompany = $this->companyUserRepository->findBy('company_id', COMPANY_ID, 'user_id');

            if ($hasUserInCompany) {
                return ['userExists' => 'User Already Exists in this company'];
            }

            return $this->createInvite($userData);
        }

        return $this->createInvite($userData);
    }

    /**
     * @param $userData
     * @return bool|mixed
     */
    protected function createInvite($userData)
    {
        $create = $this->dispatchNow(new CreateInvitation($userData));

        if (!$create) {
            return false;
        }

        return $create;
    }

    /**
     * @param $token
     * @return array|bool
     */
    public function checkInvitationToken($token)
    {
        if (!\Cache::has('user_invitation_data_' . $token)) {
            return false;
        }

        $data = \Cache::get('user_invitation_data_' . $token);
        $userData = json_decode($data);

        return [
            'name' => $userData->name,
            'email' => $userData->email
        ];
    }

    /**
     * @param $data
     * @return bool|\Illuminate\Foundation\Bus\PendingDispatch
     */
    public function createInvitationUser($data)
    {
        if (!$this->validate($this->baseValidator, $data, ['rule' => 'InviteCreate'])) {
            return false;
        }

        if (!\Cache::has('user_invitation_data_' . $data['token'])) {
            return false;
        }

        $dataCache = \Cache::get('user_invitation_data_' . $data['token']);
        $userData = json_decode($dataCache);

        return dispatch_now(new ConfirmInvitation($userData,
            ['name' => $data['name'], 'password' => $data['password']]));
    }
}
