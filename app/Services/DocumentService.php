<?php
/** @noinspection StaticInvocationViaThisInspection */
declare(strict_types=1);

namespace App\Services;

use App\Config\ConstPersonPermission;
use App\Config\ConstPersonRole;
use App\Events\Broadcast\DocumentAdded;
use App\Repositories\Contracts\DocumentPositionRepositoryInterface as DocumentPosition;
use App\Repositories\Contracts\DocumentRepositoryInterface as DocumentRepository;
use App\Repositories\Contracts\DocumentUserRepositoryInterface as DocumentUserRepository;
use App\Repositories\Contracts\GroupRepositoryInterface as GroupRepository;
use App\Repositories\Contracts\GroupUserRepositoryInterface as GroupUserRepository;
use App\Repositories\Contracts\RoleRepositoryInterface as RoleRepository;
use App\Repositories\Contracts\UserRepositoryInterface as UserRepository;
use App\Validators\DocumentValidator;
use LaraRepo\Criteria\Where\WhereCriteria;
use LaraRepo\Criteria\With\RelationCriteria;
use function PhpUtil\create_slug;

/**
 * Class DocumentService
 * @package App\Services
 */
class DocumentService extends BaseService
{
    /**
     * @var GroupRepository
     */
    protected $groupRepository;

    /**
     * @var UserRepository
     */
    protected $userRepository;

    /**
     * @var
     */
    protected $groupUserRepository;

    /**
     * @var
     */
    protected $documentUserRepository;

    /**
     * @var
     */
    protected $roleRepository;

    /**
     * @var
     */
    protected $documentPosition;

    /**
     * DocumentService constructor.
     * @param DocumentRepository $documentRepository
     * @param GroupRepository $groupRepository
     * @param UserRepository $userRepository
     * @param GroupUserRepository $groupUserRepository
     * @param DocumentUserRepository $documentUserRepository
     * @param RoleRepository $roleRepository
     * @param DocumentValidator $baseValidator
     * @param DocumentPosition $documentPosition
     */
    public function __construct(
        DocumentRepository $documentRepository,
        GroupRepository $groupRepository,
        UserRepository $userRepository,
        GroupUserRepository $groupUserRepository,
        DocumentUserRepository $documentUserRepository,
        RoleRepository $roleRepository,
        DocumentValidator $baseValidator,
        DocumentPosition $documentPosition
    ) {
        $this->baseRepository = $documentRepository;
        $this->baseValidator = $baseValidator;
        $this->groupRepository = $groupRepository;
        $this->userRepository = $userRepository;
        $this->groupUserRepository = $groupUserRepository;
        $this->documentUserRepository = $documentUserRepository;
        $this->roleRepository = $roleRepository;
        $this->documentPosition = $documentPosition;
    }

    /**
     * @param array $data
     * @return array
     */
    public function getCurrentContent(array $data)/*: array*/
    {
        $document = $this->baseRepository->findBy('slug_url', $data['documentname'], [
            'document_id',
            'content',
            'owner_id'
        ]);

        if (isset($data['groupname'])) {
            $roleGroup = $this->getGroupDocument($document['document_id'], $data['groupname']);
            return [$document['content'], $roleGroup];
        }

        $roleDocument = $this->getDocument($document['document_id'], $document['owner_id']);
        return [$document['content'], $roleDocument];
    }

    /**
     * @param $documentId
     * @param $groupName
     * @return mixed
     */
    protected function getGroupDocument(int $documentId, string $groupName)
    {
        $group = $this->groupRepository->findBy('slug_url', $groupName, [
            'owner_id',
            'group_id'
        ]);

        if ($group['owner_id'] == USER_ID) {
            return [
                ConstPersonRole::LEADER,
                ConstPersonPermission::EXECUTE
            ];
        }

        $related = [
            'role' => [
                'columns' => [
                    'role_id',
                    'role_type',
                    'permission'
                ]
            ]
        ];
        $this->groupUserRepository->pushCriteria(new RelationCriteria($related));
        $this->groupUserRepository->pushCriteria(new WhereCriteria('group_id', $group['group_id']));

        $permissionGroup = $this->groupUserRepository->findBy('user_id', USER_ID, 'role_id');

        if ($permissionGroup['role_id']) {
            return $permissionGroup['role'];
        }

        $related = [
            'role' => [
                'columns' => [
                    'role_id',
                    'role_type',
                    'permission'
                ]
            ]
        ];
        $this->documentUserRepository->pushCriteria(new RelationCriteria($related));
        $this->documentUserRepository->pushCriteria(new WhereCriteria('document_id', $documentId));

        $permissionGroupDocument = $this->documentUserRepository->findBy('user_id', USER_ID, 'role_id');

        return $permissionGroupDocument['role'];
    }

    /**
     * @param $documentId
     * @param $ownerId
     * @return mixed
     */
    protected function getDocument($documentId, $ownerId)
    {
        if ($ownerId == USER_ID) {
            return [
                'role_type' => ConstPersonRole::LEADER,
                'permission' => ConstPersonPermission::EXECUTE
            ];
        }

        $related = [
            'role' => [
                'columns' => [
                    'role_id',
                    'role_type',
                    'permission'
                ]
            ]
        ];
        $this->documentUserRepository->pushCriteria(new RelationCriteria($related));
        $this->documentUserRepository->pushCriteria(new WhereCriteria('document_id', $documentId));

        $permissionDocument = $this->documentUserRepository->findBy('user_id', USER_ID, 'role_id');

        return $permissionDocument['role'];
    }

    /**
     * @return mixed
     */
    public function getDocuments()
    {
        $this->baseRepository->pushCriteria(new WhereCriteria('company_id', COMPANY_ID));
        return $this->baseRepository->findAllBy('owner_id', USER_ID, ['document_id', 'name', 'group_id']);
    }

    /**
     * @param array $data
     * @return null|string
     */
    public function addDocument(array $data): ?string
    {
        $data['users'] = (array)$data['users'];
        $data['users'][] = (int)USER_ID;

        if (!$this->validate($this->baseValidator, $data['document'], ['rule' => 'AddDocument'])) {
            return null;
        }

        $this->baseRepository->startTransaction();

        $users = [];
        foreach ($data['users'] as $user) {
            $role = $this->roleRepository->create([
                'role_type' => $user == USER_ID ? ConstPersonRole::LEADER : ConstPersonRole::USER,
                'permission' => $user == USER_ID ? ConstPersonPermission::EXECUTE : ConstPersonPermission::WRITE
            ]);

            if (!$role) {
                $this->baseRepository->rollbackTransaction();
                return null;
            }

            $users[] = [
                'user_id' => $user,
                'role_id' => $role['role_id']
            ];
        }

        $saveData = [
            'owner_id' => USER_ID,
            'group_id' => $data['group']['group'],
            'company_id' => COMPANY_ID,
            'name' => $data['document']['name'],
            'slug_url' => create_slug($data['document']['name']),
            'description' => $data['document']['description'],
            'content' => $data['document']['data'],
            'users_ids' => $users
        ];

        $saveDocument = $this->baseRepository->saveAssociated($saveData, ['associated' => ['users']]);
        if (!$saveDocument) {
            $this->baseRepository->rollbackTransaction();
            return null;
        }

        if (!$data['group']['group']) {
            $savePosition = $this->documentPosition->create([
                'user_id' => USER_ID,
                'document_id' => $saveDocument,
                'company_id' => COMPANY_ID,
                'current_position' => $this->getMaxCurrentPosition() + 1,
                'previous_position' => $this->getMaxCurrentPosition() + 1
            ]);
            if (!$savePosition) {
                $this->baseRepository->rollbackTransaction();
                return null;
            }
        }

        $this->baseRepository->commitTransaction();
        event(new DocumentAdded($saveDocument));

        return $saveData['slug_url'];
    }

    /**
     * @param $data //Todo middleware method
     */
    public function checkPermissionEdit($data): void
    {
        $this->baseRepository->pushCriteria(new WhereCriteria('slug_url', $data['slug']));
    }

    /**
     * @param array $data
     * @return null|string
     */
    public function updateDocument(array $data): ?string
    {
        if (!$this->baseRepository->updateBased($data, ['slug_url' => $data['name']])) {
            return null;
        }

        return $data['name'];
    }

    /**
     * @param $slugUrl
     * @return bool
     */
    public function deleteDocument($slugUrl): bool
    {
        if (!$this->baseRepository->destroyBy('slug_url', $slugUrl)) {
            return false;
        }

        return true;
    }

    /**
     * @param array $data
     * @return bool
     */
    public function deleteDocumentGroup(array $data): bool
    {
        $update = $this->baseRepository->updateBased(['group_id' => null],
            ['document_id' => $data['documentId'], 'group_id' => $data['groupId']]);

        if (!$update) {
            return false;
        }

        return true;
    }

    /**
     * @param $data
     * @return bool|mixed
     */
    public function editOrCreateGroup($data)
    {
        return $this->baseRepository->updateBased(['group_id' => $data['groupId']],
                ['document_id' => $data['documentId']]) ?? false;
    }

    /**
     * @param $data
     * @return bool
     */
    public function addDocumentsInGroup($data): bool
    {
        if (!$this->baseRepository->updateBased(['group_id' => $data['groupId']],
            ['document_id' => $data['documentsId']])) {

            return false;
        }

        return true;
    }
}
