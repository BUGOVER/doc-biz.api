<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\DocumentService;
use App\Services\DocumentUserService;
use Illuminate\Http\Request;

/**
 * Class DocumentController
 * @package App\Http\Controllers
 */
class DocumentController extends BaseController
{
    /**
     * @var DocumentService
     */
    protected $documentService;

    /**
     * @var
     */
    protected $documentUserService;

    /**
     * DocumentController constructor.
     * @param DocumentService $documentService
     * @param DocumentUserService $documentUserService
     */
    public function __construct(DocumentService $documentService, DocumentUserService $documentUserService)
    {
        $this->documentService = $documentService;
        $this->documentUserService = $documentUserService;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function getContent(Request $request)
    {
        [$content, $role] = $this->documentService->getCurrentContent($request->all());

        if (!$content) {
            return response(['message' => 'Error document'], 500);
        }

        return response(['content' => $content, 'role' => $role]);
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function getDocuments()
    {
        $documents = $this->documentService->getDocuments();

        if (!$documents) {
            return response(['message' => 'Error Documents'], 500);
        }

        return response($documents);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function addDocument(Request $request)
    {
        $document = $this->documentService->addDocument($request->all());

        if (!$document) {
            return response(['message' => 'Error saved Documents'], 500);
        }

        return response(['message' => 'Document', 'documentSlug' => $document]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function editDocument(Request $request)
    {
        $updateDocument = $this->documentService->updateDocument($request->all());

        if (!$updateDocument) {
            return response(['message' => 'Error saved', 'name' => $updateDocument], 500);
        }

        return response(['message' => 'Document w', 'name' => $updateDocument]);
    }

    /**
     * @param $slugName
     * @return \Illuminate\Contracts\Routing\ResponseFactory|mixed|\Symfony\Component\HttpFoundation\Response
     */
    public function deleteDocument($slugName)
    {
//        return response([$slugName]);
        $delete = $this->documentService->deleteDocument($slugName);

        if (!$delete) {
            return response(['message' => 'Try again'], 500);
        }

        return response(['message' => 'Document ' . $slugName . ' deleted']);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function getHasDocument(Request $request)
    {
        $document = $this->documentUserService->hasUserDocument($request->input('user_id'));

        if (!$document) {
            return response(['message' => 'Error'], 500);
        }

        return response($document);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function editDocumentRole(Request $request)
    {
        $edit = $this->documentUserService->editRole($request->all());

        if (!$edit) {
            return response(['message' => 'Error Edited'], 500);
        }

        return response(['message' => 'Edited']);
    }

    /**
     * @param $documentId
     * @param $groupId
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function deleteDocumentInGroup($documentId, $groupId)
    {
        $delete = $this->documentService->deleteDocumentGroup(['documentId' => $documentId, 'groupId' => $groupId]);

        if (!$delete) {
            return response(['message' => 'Error'], 500);
        }

        return response(['message' => 'Deleted Document in group']);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function editDocumentGroup(Request $request)
    {
        $create = $this->documentService->editOrCreateGroup($request->input('data'));

        if ($create) {
            return response(['message' => 'Document added in group ']);
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function addUserInDocument(Request $request)
    {
        $addDocument = $this->documentUserService->addUserDocument($request->all());

        if ($addDocument) {
            return response(['message' => 'Added Document for User']);
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function addDocuments(Request $request)
    {
        $documents = $this->documentService->addDocumentsInGroup($request->all());

        if ($documents) {
            return response(['message' => 'Documents added in group']);
        }
    }
}
