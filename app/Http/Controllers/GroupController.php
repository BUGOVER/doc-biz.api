<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\CompanyService;
use App\Services\GroupService;
use Illuminate\Http\Request;

/**
 * Class GroupController
 * @package App\Http\Controllers
 */
class GroupController extends BaseController
{
    /**
     * @var CompanyService
     */
    protected $groupService;

    /**
     * GroupController constructor.
     * @param GroupService $groupService
     */
    public function __construct(GroupService $groupService)
    {
        $this->groupService = $groupService;
    }

    /**
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function getGroups()
    {
        $result = $this->groupService->getUserAllGroup();

        if (!$result) {
            return response(['Groups error'], 500);
        }

        return response($result);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function addGroup(Request $request)
    {
        $add = $this->groupService->addGroups($request->input('data'));

        if (!$add) {
            return response(['message' => 'Groups error to add'], 500);
        }

        return response(['message' => 'Group added']);
    }

    /**
     * @param $groupId
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function deleteGroup($groupId)
    {
        $data = explode(',', $groupId);

        $deleteGroup = $this->groupService->deleteGroup($data);

        if (!$deleteGroup) {
            return response(['message' => 'Not deleted'], 500);
        }

        return response(['message' => 'Group Deleted in Company']);
    }

    /**
     * @param Request $request
     * @return bool|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function renameGroup(Request $request)
    {
        $rename = $this->groupService->rename($request->all());

        if ($rename) {
            return response(['message' => 'Name renamed', 'slug' => $rename]);
        }
    }
}
