<?php
declare(strict_types=1);

namespace App\Services;

use App\Config\ConstPersonPermission;
use App\Config\ConstPersonRole;
use App\Mail\CompanyEmailVerified;
use App\Repositories\Contracts\CompanyRepositoryInterface as CompanyRepository;
use App\Repositories\Contracts\CompanyUserRepositoryInterface as CompanyUserRepository;
use App\Repositories\Contracts\GroupRepositoryInterface as GroupRepository;
use App\Repositories\Contracts\RoleRepositoryInterface as RoleRepository;
use App\Repositories\Contracts\UserRepositoryInterface as UserRepository;
use App\Repositories\Criteria\WhereNullRelationCriteria;
use App\Validators\CompanyValidator;
use Cache;
use Carbon\Carbon;
use LaraRepo\Criteria\Where\WhereCriteria;
use LaraRepo\Criteria\With\RelationCriteria;
use Mail;

/**
 * Class CompanyService
 * @package App\Services
 */
class CompanyService extends BaseService
{
    /**
     * @var
     */
    protected $companyValidator;

    /**
     * @var
     */
    protected $authService;

    /**
     * @var
     */
    protected $roleRepository;

    /**
     * @var
     */
    protected $userRepository;

    /**
     * @var
     */
    protected $groupRepository;

    /**
     * @var
     */
    protected $companyUserRepository;

    /**
     * CompanyService constructor.
     * @param CompanyRepository $companyRepository
     * @param UserRepository $userRepository
     * @param CompanyValidator $companyValidator
     * @param AuthService $authService
     * @param RoleRepository $roleRepository
     * @param GroupRepository $groupRepository
     * @param CompanyUserRepository $companyUserRepository
     */
    public function __construct(
        CompanyRepository $companyRepository,
        UserRepository $userRepository,
        CompanyValidator $companyValidator,
        AuthService $authService,
        RoleRepository $roleRepository,
        GroupRepository $groupRepository,
        CompanyUserRepository $companyUserRepository
    ) {
        $this->baseRepository = $companyRepository;
        $this->userRepository = $userRepository;
        $this->companyValidator = $companyValidator;
        $this->authService = $authService;
        $this->roleRepository = $roleRepository;
        $this->groupRepository = $groupRepository;
        $this->companyUserRepository = $companyUserRepository;
    }

    /**
     * @param $data
     * @return bool
     * @throws \Exception
     */
    public function createCompanyEmail($data): bool
    {
        if (!$this->validate($this->companyValidator, $data, ['rule' => 'sendEmail'])) {
            return false;
        }

        $key = random_int(100000, 999999);
        Mail::to($data['email'])->queue(new CompanyEmailVerified($key));

        if (Mail::failures()) {
            return false;
        }

        Cache::put('_email_verified_' . $data['email'], $key, Carbon::now()->addMinute($data['expire']));//120 m
        return true;
    }

    /**
     * @param $data
     * @return mixed|null
     */
    public function createCompany($data)
    {
        $userData = ng_data_iterate($data);

        if (!$this->validate($this->companyValidator, $userData, ['rule' => 'CreateUserWithCompany'])) {
            return null;
        }

        $this->baseRepository->startTransaction();

        $roleId = $this->createPermission($userData['role'], $userData['permission']);

        if (!$roleId) {
            $this->baseRepository->rollbackTransaction();
            return null;
        }

        $userId = $this->authService->createUser($userData);

        if ($userId == null) {
            $this->baseRepository->rollbackTransaction();
            return null;
        }

        $create = $this->createUserCompany($userData['company_name'], $userId, $roleId);
        if (!$create) {
            $this->baseRepository->rollbackTransaction();
            return null;
        }

        $savedUserData = $this->userRepository->updateBased(['company_id' => $create['company_id']],
                ['user_id' => $userId]) ?? false;

        if (!$savedUserData) {
            $this->baseRepository->rollbackTransaction();
            return null;
        }

        $this->baseRepository->commitTransaction();
        return $create;
    }

    /**
     * @param int $role
     * @param int $permission
     * @return mixed
     */
    protected function createPermission(
        int $role = ConstPersonRole::USER,
        int $permission = ConstPersonPermission::READ
    ) {
        $createPermission = $this->roleRepository->create(['role_type' => $role, 'permission' => $permission]);
        return $createPermission->role_id ?? false;
    }

    /**
     * @param string $companyName
     * @param $userId
     * @param $roleId
     * @return mixed
     */
    protected function createUserCompany(string $companyName, ?int $userId, ?int $roleId)
    {
        $savedData = [
            'leader_id' => $userId,
            'name' => $companyName,
            'users_ids' => [
                [
                    'user_id' => $userId,
                    'role_id' => $roleId
                ]
            ]
        ];

        return $this->baseRepository->saveAssociated($savedData, ['associated' => ['users']]) ?? false;
    }

    /**
     * @param $companyName
     * @return array|null
     */
    public function checkCompany($companyName): ?array
    {
        if (!$this->validate($this->companyValidator, $companyName, ['rule' => 'CheckCompanyName'])) {
            return null;
        }

        $companyId = $this->baseRepository->findBy('name', $companyName['company_name'], 'company_id');
        if (!$companyId) {
            return null;
        }

        $companyKey = str_random();
        Cache::putMany(
            [
                $companyKey . '_company_name_expire_' => $companyName['company_name'],
                $companyKey . '_company_id_expire_' => $companyId->company_id
            ],
            Carbon::now()->addMinutes(120)
        );

        return [$companyId->company_id, $companyKey];
    }

    //======================================================Menu=====================================================//

    /**
     * @return mixed
     */
    public function getMenuCurrentUser()
    {
        $related = [
            'companies' => [
                'columns' => [
                    'company_id',
                    'name'
                ],
                'where' => [
                    'company_id' => COMPANY_ID
                ]
            ],
            'groups' => [
                'columns' => [
                    'group_id',
                    'company_id',
                    'name',
                    'type',
                    'slug_url'
                ],
                'where' => [
                    'company_id' => COMPANY_ID
                ]
            ],
            'groups.documents' => [
                'columns' => [
                    'document_id',
                    'group_id',
                    'company_id',
                    'name',
                    'description',
                    'slug_url'
                ],
                'where' => [
                    'company_id' => COMPANY_ID
                ]
            ],
            'documents.position' => [
                'columns' => [
                    'document_id',
                    'current_position',
                    'previous_position'
                ]
            ],
            'groups.position' => [
                'columns' => [
                    'group_id',
                    'current_position',
                    'previous_position'
                ]
            ]
        ];
        $this->userRepository->pushCriteria(new RelationCriteria($related));

        $this->userRepository->pushCriteria(
            new WhereNullRelationCriteria('documents', 'group_id',
                ['name', 'description', 'slug_url', 'documents.document_id'],
                ['company_id' => COMPANY_ID])
        );

        $menuData = $this->userRepository->find(USER_ID, ['user_id']);

        $related = [
            'role' => [
                'columns' => [
                    'role_id',
                    'role_type',
                    'permission'
                ]
            ]
        ];
        $this->companyUserRepository->pushCriteria(new RelationCriteria($related));
        $this->companyUserRepository->pushCriteria(new WhereCriteria('company_id', COMPANY_ID));
        $permissionCompany = $this->companyUserRepository->findBy('user_id', USER_ID, 'role_id');

        $menuData->role = $permissionCompany['role'];
        return $menuData;
    }
}
