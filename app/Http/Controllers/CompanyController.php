<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\CompanyService;
use App\Services\CompanyUserService;
use Cache;
use Carbon\Carbon;
use Illuminate\Http\Request;

/**
 * Class CompanyController
 * @package App\Http\Controllers
 */
class CompanyController extends BaseController
{
    /**
     * @var CompanyService
     */
    protected $companyService;

    /**
     * @var CompanyService
     */
    protected $companyUserService;

    /**
     * CompanyController constructor.
     * @param CompanyService $companyService
     * @param CompanyUserService $companyUserService
     */
    public function __construct(
        CompanyService $companyService,
        CompanyUserService $companyUserService
    ) {
        $this->companyService = $companyService;
        $this->companyUserService = $companyUserService;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function createCompanyEmail(Request $request)
    {
        $email = $this->companyService->createCompanyEmail($request->all());

        if (!$email) {
            return response([
                'message' => 'Your email not send please try again',
                'email' => $request->input('email'),
                'status' => 500
            ], 500);
        }

        return response([
            'message' => 'Your email send to key please check your email',
            'status' => 200,
            'email' => $request->input('email'),
            'hash_key' => $email
        ]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function checkConfirmEmail(Request $request)
    {
        $cache = Cache::get('_email_verified_' . $request->input('email'));

        if ($cache !== null) {
            return response([
                'message' => 'Email validity period has not expired',
                'status' => 200
            ]);
        }

        return response([
            'message' => 'Email validity period has expired',
            'status' => 500
        ], 500);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function checkConfirmKey(Request $request)
    {
        $getKeyByEmail = Cache::get('_email_verified_' . $request->input('email'));

        if (!$getKeyByEmail || $getKeyByEmail != $request->input('key')) {
            return response(['message' => 'Key is not valid', 'status' => 500], 500);
        }

        return response([
            'email' => $request->input('email'),
            'status' => 200,
            'message' => 'Key ok is accepted'
        ]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function createCompany(Request $request)
    {
        $emailData = $request->input('data');

        if (!Cache::has('_email_verified_' . $emailData['email'])) {
            return response(['message' => 'Your activation time is passed'], 500);
        }

        $data = $this->companyService->createCompany($request->all());
        if (!$data) {
            return response([
                'message' => $data,
                'errors' => $this->companyService->getValidationErrors(),
                'status' => 500
            ], 500);
        }

        Cache::putMany([
            '_company_id_for_token_' => $data,
            '_auth_email_' => $request->input('data.email')
        ], Carbon::now()->addMinutes(15));
        Cache::delete('_email_verified_' . $emailData['email']);
        return response([
            'message' => 'Saved your data Welcome DocBiz commercial app',
            'status' => 200,
            'data' => $data
        ]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function checkCompanyName(Request $request)
    {
        [$checkCompanyName, $companyKey] = $this->companyService->checkCompany($request->all());

        if (!$checkCompanyName) {
            return response(['message' => 'There is no such company'], 500);
        }

        return response([
            'message' => 'Company accepted',
            'company_id' => $checkCompanyName,
            'companyKey' => $companyKey
        ]);
    }

    /**
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function getMenu()
    {
        $menu = $this->companyService->getMenuCurrentUser();

//        return response($menu);
        if (!$menu) {
            return response(['message' => 'Invalid Data'], 500);
        }

        return response([$menu]);
    }
}
