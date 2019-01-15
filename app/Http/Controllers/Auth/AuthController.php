<?php
declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\BaseController;
use App\Services\AuthService;
use App\Services\UserService;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\JsonResponse;

/**
 * Class LoginController
 * @package App\Http\Controllers\Auth
 */
class AuthController extends BaseController
{
    use AuthenticatesUsers;

    /**
     * @var UserService
     */
    protected $service;

    /**
     * AuthController constructor.
     * @param AuthService $service
     */
    public function __construct(AuthService $service)
    {
        $this->service = $service;
    }

    /**
     * @return \Illuminate\Contracts\Routing\ResponseFactory|JsonResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function signIn()
    {
        $current = $this->service->getCurrentUser();

        if (!$current) {
            return response(['message' => 'Failed Authorization'], 500);
        }

        return response($current);
    }

    /**
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function logoutApi()
    {
        $logout = $this->service->logoutApiUser();

        if (!$logout) {
            return response(['message' => 'Failed Logout'], 500);
        }

        return response(['message' => 'Logout']);
    }
}
