<?php
declare(strict_types=1);

namespace App\Jobs;

use App\Config\ConstPersonPermission;
use App\Config\ConstPersonRole;
use App\Events\InvitationConfirmed;
use App\Repositories\Contracts\CompanyRepositoryInterface as CompanyRepository;
use App\Repositories\Contracts\CompanyUserRepositoryInterface as CompanyUserRepository;
use App\Repositories\Contracts\DocumentUserRepositoryInterface as DocumentUserRepository;
use App\Repositories\Contracts\GroupUserRepositoryInterface as GroupUserRepository;
use App\Repositories\Contracts\RoleRepositoryInterface as RoleRepository;
use App\Repositories\Contracts\UserRepositoryInterface as UserRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Class ConfirmInvitation
 * @package App\Jobs
 */
class ConfirmInvitation implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var
     */
    protected $data;

    /**
     * @var
     */
    protected $name;

    /**
     * @var
     */
    protected $password;

    /**
     * @var
     */
    protected $loopData;

    /**
     * ConfirmInvitation constructor.
     * @param $data
     * @param $inviteAuthData
     */
    public function __construct($data, $inviteAuthData)
    {
        $this->data = $data;
        $this->name = $inviteAuthData['name'];
        $this->password = $inviteAuthData['password'];
    }

    /**
     * @param UserRepository $userRepository
     * @param RoleRepository $roleRepository
     * @param DocumentUserRepository $documentUserRepository
     * @param GroupUserRepository $groupUserRepository
     * @param CompanyRepository $companyRepository
     * @param CompanyUserRepository $companyUserRepository
     * @return mixed|null
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function handle(
        UserRepository $userRepository,
        RoleRepository $roleRepository,
        DocumentUserRepository $documentUserRepository,
        GroupUserRepository $groupUserRepository,
        CompanyRepository $companyRepository,
        CompanyUserRepository $companyUserRepository
    ) {
        $userRepository->startTransaction();

        //=============================================USER CREATE=========================================//
        $saveUser = [
            'inviting_user_id' => $this->data->sender_id,
            'company_id' => $this->data->company_id,
            'name' => $this->name,
            'email' => $this->data->email,
            'password' => \Hash::make($this->password)
        ];
        $userId = $userRepository->create($saveUser);

        if (!$userId) {
            $userRepository->rollbackTransaction();
            return null;
        }

        //=========================================COMPANY USER CREATE=====================================//
        if (!isset($this->data->company_role)) { // Todo <- temporarily ?
            $companyPermissions =
                $roleRepository->create([
                    'role_type' => ConstPersonRole::USER,
                    'permission' => ConstPersonPermission::READ
                ]);
        }

        $companyData = [
            'user_id' => $userId['user_id'],
            'company_id' => $this->data->company_id,
            'role_id' => $companyPermissions['role_id']
        ];

        if (!$companyUserRepository->create($companyData)) {
            $userRepository->rollbackTransaction();
            return null;
        }

        $companyName = $companyRepository->find($this->data->company_id, ['name']);
        //==========================================GROUP USER CREATE======================================//
        if (!empty($this->data->user_group)) {
            $groupUserCreate =
                $this->saveGroupUserOrAdmin($roleRepository, $groupUserRepository, $userId['user_id'],
                    $this->data->user_group);

            if (!$groupUserCreate) {
                $userRepository->rollbackTransaction();
                return null;
            }
        }

        //=========================================GROUP ADMIN CREATE======================================//
        if (!empty($this->data->admin_group)) {
            $groupAdminCreate =
                $this->saveGroupUserOrAdmin(
                    $roleRepository, $groupUserRepository, $userId['user_id'],
                    [], $this->data->admin_group);

            if (!$groupAdminCreate) {
                $userRepository->rollbackTransaction();
                return null;
            }
        }

        //=========================================DOCUMENT USER CREATE======================================//
        if (!empty($this->data->user_document)) {
            $documentUserCreate =
                $this->saveDocumentUserOrAdmin(
                    $roleRepository, $documentUserRepository, $userId['user_id'],
                    $this->data->user_document);

            if (!$documentUserCreate) {
                $userRepository->rollbackTransaction();
                return null;
            }
        }

        //=========================================DOCUMENT ADMIN CREATE======================================//
        if (!empty($this->data->admin_document)) {
            $documentAdminCreate =
                $this->saveDocumentUserOrAdmin(
                    $roleRepository, $documentUserRepository, $userId['user_id'],
                    [], $this->data->admin_document);

            if (!$documentAdminCreate) {
                $userRepository->rollbackTransaction();
                return null;
            }
        }

        event(new InvitationConfirmed($this->name, $companyName['name'], $this->data->email));
        $userRepository->commitTransaction();
        \Cache::delete('user_invitation_data_' . $this->data->token); //Todo Uncomment

        return $companyData['company_id'];
    }

    /**
     * @param $roleRepository
     * @param $groupUserRepository
     * @param $userId
     * @param array $userGroup
     * @param array $adminGroup
     * @return bool
     */
    protected function saveGroupUserOrAdmin(
        $roleRepository,
        $groupUserRepository,
        $userId,
        array $userGroup = [],
        array $adminGroup = []
    ): bool {
        if (empty($userGroup)) {
            $this->loopData = $adminGroup;
        } else {
            $this->loopData = $userGroup;
        }

        foreach ($this->loopData as $groupId) {

            $roleGroupUser = $roleRepository->create([
                'role_type' => !empty($userGroup) ? ConstPersonRole::USER : ConstPersonRole::ADMIN,
                'permission' => !empty($userGroup) ? ConstPersonPermission::READ : ConstPersonPermission::WRITE
            ]);

            if (!$roleGroupUser) {
                return false;
            }

            $groupUserSave = $groupUserRepository->create([
                'user_id' => $userId,
                'group_id' => $groupId,
                'role_id' => $roleGroupUser['role_id']
            ]);

            if (!$groupUserSave) {
                return false;
            }
        }

        $this->loopData = '';
        return true;
    }

    /**
     * @param $roleRepository
     * @param $documentUserRepository
     * @param $userId
     * @param array $userDocument
     * @param array $adminDocument
     * @return bool
     */
    protected function saveDocumentUserOrAdmin(
        $roleRepository,
        $documentUserRepository,
        $userId,
        array $userDocument = [],
        array $adminDocument = []
    ): bool {
        if (empty($userDocument)) {
            $this->loopData = $adminDocument;
        } else {
            $this->loopData = $userDocument;
        }

        foreach ($this->loopData as $groupId) {

            $roleGroupUser = $roleRepository->create([
                'role_type' => !empty($userDocument) ? ConstPersonRole::USER : ConstPersonRole::ADMIN,
                'permission' => !empty($userDocument) ? ConstPersonPermission::READ : ConstPersonPermission::WRITE
            ]);

            if (!$roleGroupUser) {
                return false;
            }

            $groupUserSave = $documentUserRepository->create([
                'user_id' => $userId,
                'document_id' => $groupId,
                'role_id' => $roleGroupUser['role_id']
            ]);

            if (!$groupUserSave) {
                return false;
            }
        }

        $this->loopData = '';
        return true;
    }
}
