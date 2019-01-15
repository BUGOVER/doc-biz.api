<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\DocumentPositionService;
use App\Services\GroupPositionService;
use Illuminate\Http\Request;

/**
 * Class PositioningController
 * @package App\Http\Controllers
 */
class PositioningController extends BaseController
{
    /**
     * @var
     */
    protected $groupPositionService;

    /**
     * @var
     */
    protected $documentPositionService;

    /**
     * PositioningController constructor.
     * @param GroupPositionService $groupPositionService
     * @param DocumentPositionService $documentPositionService
     */
    public function __construct(
        GroupPositionService $groupPositionService,
        DocumentPositionService $documentPositionService
    ) {
        $this->groupPositionService = $groupPositionService;
        $this->documentPositionService = $documentPositionService;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     * @throws \Exception
     */
    public function reStructuredGroupByMenu(Request $request)
    {
        $restructured = $this->groupPositionService->groupStructure($request->input('group'));

        if (!$restructured) {
            return response(['message' => 'Name renamed'], 502);
        }

        return response([
            'message' =>
                'Group ' . $request->input('group.groupName') . ' changed position from ' . $request->input('group.previousIndex') . ' to ' . $request->input('group.newIndex')
        ], 201);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     * @throws \Exception
     */
    public function reStructuredDocumentByMenu(Request $request)
    {
        $restructured = $this->documentPositionService->documentStructure($request->input('document'));

        if (!$restructured) {
            return response(['message' => 'Name renamed'], 502);
        }

        return response([
            'message' =>
                'Document ' . $request->input('document.documentName') . ' changed position from ' . $request->input('document.previousIndex') . ' to ' . $request->input('document.newIndex')
        ], 201);
    }
}
