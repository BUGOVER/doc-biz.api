<?php
/** @noinspection PhpParamsInspection */
declare(strict_types=1);

namespace App\Services;

use App\Mail\DeleteUserInCompany;
use App\Mail\ResetPassword;
use App\Repositories\Contracts\CompanyRepositoryInterface as CompanyRepository;
use App\Repositories\Contracts\CompanyUserRepositoryInterface as CompanyUserRepository;
use App\Repositories\Contracts\UserRepositoryInterface as UserRepository;
use App\Repositories\Criteria\WithCountCriteria;
use App\Validators\UserValidator;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use LaraRepo\Criteria\Where\WhereCriteria;
use LaraRepo\Criteria\With\RelationCriteria;

/**
 * Class UserService
 * @package App\Services
 */
class UserService extends BaseService
{
    /**
     * @var CompanyUserRepository
     */
    protected $companyUserRepository;

    /**
     * @var CompanyUserRepository
     */
    protected $userValidator;

    /**
     * @var CompanyUserRepository
     */
    protected $companyRepository;

    /**
     * Pagination Per Page count
     * @var int
     */
    protected $perPage = 25;

    /**
     * UserService constructor.
     * @param UserRepository $userRepository
     * @param CompanyRepository $companyRepository
     * @param CompanyUserRepository $companyUserRepository
     * @param UserValidator $userValidator
     */
    public function __construct(
        UserRepository $userRepository,
        CompanyRepository $companyRepository,
        CompanyUserRepository $companyUserRepository,
        UserValidator $userValidator
    ) {
        $this->baseRepository = $userRepository;
        $this->companyRepository = $companyRepository;
        $this->companyUserRepository = $companyUserRepository;
        $this->userValidator = $userValidator;
    }

    /**
     * @param $data
     * @return bool|mixed //@TODO middleware method
     */
    public function checkedData($data)/*: bool*/
    {
        $userData = $this->baseRepository->find($data['user_id'], ['email']);

        if (!$userData || $userData['email'] !== $data['user_email']) {
            return false;
        }

        $this->baseRepository->pushCriteria(
            new WithCountCriteria('companies', 'companies.company_id', $data['company_id'], [], ['name']));

        return $this->baseRepository->find($data['user_id'], ['user_id']);
    }

    /**
     * @param array $data
     * @return Model|null
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function checkUserData(array $data): ?Object
    {
        if (!$this->validate($this->userValidator, $data, ['rule' => 'UserSignData'])) {
            return null;
        }

        $hasCacheCompanyName = Cache::get($data['companyKey'] . '_company_name_expire_');
        $hasCacheCompanyId = Cache::get($data['companyKey'] . '_company_id_expire_');

        if (!$hasCacheCompanyName || !$hasCacheCompanyId) {
            return null;
        }

        if (!\Auth::attempt([
            'company_id' => $data['companyId'],
            'email' => $data['email'],
            'password' => $data['password']
        ])) {
            return null;
        }

        $this->baseRepository->pushCriteria(new WhereCriteria('company_id', $data['companyId']));
        $password = $this->baseRepository->findBy('email', $data['email'], ['user_id', 'password']);

        if (!\Hash::check($data['password'], $password['password'])) {
            return null;
        }

        $related = [
            'role' => [
                'columns' => [
                    'role_id',
                    'role_type',
                    'permission'
                ],
            ]
        ];

        $this->companyUserRepository->pushCriteria(new RelationCriteria($related));
        $this->companyUserRepository->pushCriteria(new WhereCriteria('company_id', $data['companyId']));
        $hasUserCompany = $this->companyUserRepository->findBy('user_id', $password->user_id, ['role_id']);

        if (!$hasUserCompany) {
            return null;
        }

        Cache::deleteMultiple([
            $data['companyKey'] . '_company_name_expire_',
            $data['companyKey'] . '_company_id_expire_'
        ]);

        return $hasUserCompany ?? null;
    }

    //@TODO its a temporary method, critical code repeat |>
    public function autoLogin(array $data)
    {
        if (!\Auth::attempt(['email' => $data['email'], 'password' => $data['password']])) {
            return null;
        }

        $userData = $this->baseRepository->findBy('email', $data['email'], ['user_id', 'company_id']);

        $related = [
            'role' => [
                'columns' => [
                    'role_id',
                    'role_type',
                    'permission'
                ],
            ]
        ];

        $this->companyUserRepository->pushCriteria(new RelationCriteria($related));
        $hasUserCompany = $this->companyUserRepository->findBy('company_id', $userData['company_id'], ['role_id']);

        return $hasUserCompany->setAttribute('company_id', $userData['company_id']);
    }

    /**
     * @param $companyId
     * @param $skip
     * @param $count
     * @return mixed
     */
    public function getCompanyUsers($companyId, $skip, $count)
    {
        $related = ['company_users_without_me' => ['columns' => ['user_id', 'name', 'email', 'created_at']]];
        $this->companyRepository->pushCriteria(new RelationCriteria($related));

        if (!empty($count)) {
            $this->companyRepository->pushCriteria(new WhereCriteria('company_id', $companyId));
            return $this->baseRepository->paginate($count, ['user_id', 'name', 'email']);
        }

        return $this->companyRepository->findAllBy('company_id', $companyId, 'company_id');
    }

    /**
     * @param $usersId
     * @return bool
     */
    public function deleteUserInCompany($usersId): bool
    {
        $removableUser = $this->baseRepository->find($usersId, ['name', 'email']);

        if (!$this->baseRepository->destroyAllByWhereIn('user_id', $usersId)) {
            return false;
        }

        foreach ($removableUser as $rUser) {

            $params = [
                'user_name' => $rUser['name'],
                'sender_name' => 'Administrator',
                'company_name' => COMPANY_NAME
            ];

            //Todo change send to queue
            \Mail::to($rUser['email'])->queue(new DeleteUserInCompany($params));

            if (\Mail::failures()) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param $data
     * @return null
     */
    public function changePassword($data)
    {
        $password = $this->baseRepository->findBy('user_id', USER_ID, ['password']);

        if (!Hash::check($data['oldPassword'], $password['password'])) {
            return ['message' => 'Password Not changed', 'status' => 500];
        }

        $updatePassword =
            $this->baseRepository->updateBased(['password' => Hash::make($data['newPassword'])],
                ['user_id' => USER_ID]);

        if (!$updatePassword) {
            return null;
        }

        return ['message' => 'Password changed'];
    }

    /**@TODO fix bug companies select |>
     * @param array $data
     * @return bool|null
     */
    public function resetPassword(array $data)
    {
        $this->baseRepository->pushCriteria(new WhereCriteria('company_id', $data['companyId']));
        $user = $this->baseRepository->findBy('email', $data['email'], ['user_id', 'name']);

        if (!$user) {
            return null;
        }

        $related = [
            'companies' => [
                'columns' => [
                    'company_id'
                ],
                'where' => [
                    'company_id' => $data['companyId']
                ]
            ]
        ];

        $this->baseRepository->pushCriteria(new RelationCriteria($related));
        $companies = $this->baseRepository->first('user_id');

        if (0 === count($companies->companies)) {
            return null;
        }

        $urlKey = str_random(64);

        $params = [
            'user_name' => $user['name'],
            'company_name' => $data['companyName'],
            'reset_password_link' => config('app_config.reset_password_redirection_link') . $urlKey
        ];

        //@TODO change send to queue
        \Mail::to($data['email'])->queue(new ResetPassword($params));
        if (\Mail::failures()) {
            return null;
        }

        \Cache::put($urlKey, $user['user_id'],
            Carbon::now()->addMinutes(config('app_config.reset_password_link_period')));

        return true;
    }

    /**
     * @param $password
     * @return bool|null
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function editPassword($password): ?bool
    {
        $hasCacheKeyUserId = Cache::get($password['urlKey']);

        if (!$hasCacheKeyUserId) {
            return null;
        }

        if (!$this->baseRepository->update(['password' => Hash::make($password['password'])], $hasCacheKeyUserId)) {
            return null;
        }

        Cache::delete($password['urlKey']);
        return true;
    }
}
