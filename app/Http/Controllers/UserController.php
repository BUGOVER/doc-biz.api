<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\UserInvitationService;
use App\Services\UserService;
use Illuminate\Http\Request;

/**
 * Class UserController
 * @package App\Http\Controllers
 */
class UserController extends BaseController
{
    /**
     * @var UserService
     */
    protected $userService;

    /**
     * @var UserService
     */
    protected $userInvitationService;

    /**
     * UserController constructor.
     * @param UserService $userService
     * @param UserInvitationService $invitationService
     */
    public function __construct(UserService $userService, UserInvitationService $invitationService)
    {
        $this->userService = $userService;
        $this->userInvitationService = $invitationService;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function checkUserData(Request $request)
    {
        $checkedUserData = $this->userService->checkUserData($request->all());

        if (!$checkedUserData) {
            return response(['message' => 'Authenticated false data not valid'], 500);
        }

        return response(['message' => 'Authenticated accepted', 'data' => $checkedUserData]);
    }


    //@TODO its a temporary method, critical code repeat |>

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function autoLogin(Request $request)
    {
        $checkedUserData = $this->userService->autoLogin($request->all());
//return response($checkedUserData);
        if (!$checkedUserData) {
            return response(['message' => 'Authenticated false data not valid'], 500);
        }

        return response(['message' => 'Authenticated accepted', 'data' => $checkedUserData]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function inviteUser(Request $request)
    {
        $invitation = $this->userInvitationService->userInvitation($request->input('invitation_data'));

        if (!empty($invitation['userExists'])) {
            return response(['message' => $invitation['userExists']], 409);
        }

        if (!$invitation) {
            return response(['message' => 'Invitation false'], 500);
        }

        return response(['message' => 'Invitation sent']);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function checkInviteToken(Request $request)
    {
        $check = $this->userInvitationService->checkInvitationToken($request->input('token'));

        if (!$check) {
            return response(['message' => 'Your invite limit passed'], 500);
        }

        return response([
            'message' => 'Thank you for accepting the invitation and joining our community',
            'data' => $check
        ]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function invitationUserCreate(Request $request)
    {
        $create = $this->userInvitationService->createInvitationUser($request->all());

        if (!$create) {
            return response(['message' => 'Error Message'], 500);
        }

        return response(['message' => 'Welcome To DocBiz', 'company_id' => $create]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function getAllUsersForCompany(Request $request)
    {
        $companyUsers = $this->userService
            ->getCompanyUsers(COMPANY_ID, $request->header('skip'), $request->header('count'));

        if (!$companyUsers) {
            return response(['message' => 'OPS no Users invite users'], 500);
        }

        return response($companyUsers);
    }

    /**
     * @param $userId
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function deleteUserInCompany($userId)
    {
        $data = explode(',', $userId);

        $deleteUser = $this->userService->deleteUserInCompany($data);

        if (!$deleteUser) {
            return response(['message' => 'Not deleted'], 500);
        }

        return response(['message' => 'User Deleted in Company']);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function changePassword(Request $request)
    {
        $changePassword = $this->userService->changePassword($request->all());
        return response($changePassword);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function resetPassword(Request $request)
    {
        $reset = $this->userService->resetPassword($request->all());

        if (!$reset) {
            return response(['message' => 'Your Are falling data'], 500);
        }

        return response([
            'message' => 'Your mail sent to reset password link (it is available for eight hours) please check your email'
        ]);
    }

    /**
     * @param Request $request
     * @return bool|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function resetPasswordCheckKey(Request $request)
    {
        if (!\Cache::get($request->input('data'))) {
            return response(['message' => 'Time excluded'], 500);
        }

        return response(['status' => 200]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function resetPasswordNewPassword(Request $request)
    {
        $changePassword = $this->userService->editPassword($request->all());

        if (!$changePassword) {
            return response(['message' => 'Date is expired'], 500);
        }

        return response(['message' => 'Password changed']);
    }
}
