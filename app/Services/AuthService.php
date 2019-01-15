<?php
declare(strict_types=1);

namespace App\Services;

use App\Repositories\Contracts\RoleRepositoryInterface as RoleRepository;
use App\Repositories\Contracts\UserRepositoryInterface as UserRepository;
use App\Validators\UserValidator;
use Auth;
use Illuminate\Support\Facades\Hash;

/**
 * Class AuthService
 * @package App\Services
 */
class AuthService extends BaseService
{
    /**
     * @var
     */
    protected $roleRepository;

    /**
     * AuthService constructor.
     * @param UserRepository $userRepository
     * @param UserValidator $userValidator
     * @param RoleRepository $roleRepository
     */
    public function __construct(
        UserRepository $userRepository,
        UserValidator $userValidator,
        RoleRepository $roleRepository
    ) {
        $this->baseRepository = $userRepository;
        $this->baseValidator = $userValidator;
        $this->roleRepository = $roleRepository;
    }

    /**
     * @param array $data
     * @return int|null
     */
    public function createUser(array $data)/*: ?int*/
    {
        if (!$this->validate($this->baseValidator, $data)) {
            return null;
        }

        $userData = [
            'name' => $data['user_name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password'])
        ];

        $create = $this->baseRepository->create($userData);

        if (!$create) {
            return null;
        }

        return $create['user_id'];
    }

    /**
     * @return int|null
     */
    public function getCurrentUser()
    {
        $oUser = $this->baseRepository->find(Auth::user()->user_id);

        if (!$oUser) {
            return null;
        }

        return $oUser;
    }

    /**
     * @return bool
     */
    public function logoutApiUser(): bool
    {
        if (Auth::check()) {
            Auth::user()->auth_access_tokens()->delete();
            return true;
        }

        return false;
    }
}
