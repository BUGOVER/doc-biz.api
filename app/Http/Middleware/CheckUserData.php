<?php
/** @noinspection PhpUndefinedFieldInspection */
declare(strict_types=1);

namespace App\Http\Middleware;

use App\Services\UserService;
use Closure;

/**
 * Class CheckUserData
 * @package App\Http\Middleware
 */
class CheckUserData
{
    /**
     * @var UserService
     */
    protected $userService;

    /**
     * CheckUserData constructor.
     * @param UserService $userService
     */
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (!$request->hasHeader('U-id') || !$request->hasHeader('U-email') || !$request->hasHeader('C-id')) {
            return response(['Unsupported Media Type'], 415);
        }

        $data = [
            'user_id' => $request->header('U-id'),
            'user_email' => $request->header('U-email'),
            'company_id' => $request->header('C-id')
        ];

        $checkedData = $this->userService->checkedData($data);

        if (!$checkedData->companies_count) {
            return response(['User Data Invalid'], 500);
        }

        if (!defined('USER_ID')) {
            define('USER_ID', $request->header('U-id'));
        }

        if (!defined('COMPANY_ID')) {
            define('COMPANY_ID', $request->header('C-id'));
        }

        if (!defined('COMPANY_NAME')) {
            define('COMPANY_NAME', $checkedData['companies_count']);
        }

        return $next($request);
    }
}
