<?php
/** @noinspection StaticInvocationViaThisInspection */
declare(strict_types=1);

namespace App\Services;

use App\Config\ConstGroupType;
use App\Config\ConstPersonPermission;
use App\Config\ConstPersonRole;
use App\Repositories\Contracts\DocumentRepositoryInterface as DocumentRepository;
use App\Repositories\Contracts\GroupPositionRepositoryInterface as GroupPositionRepository;
use App\Repositories\Contracts\GroupRepositoryInterface as GroupRepository;
use App\Repositories\Contracts\GroupUserRepositoryInterface as GroupUserRepository;
use App\Repositories\Contracts\RoleRepositoryInterface as RoleRepository;
use App\Repositories\Criteria\LatestCriteria;
use App\Validators\GroupValidator;
use LaraRepo\Criteria\Where\WhereCriteria;
use function PhpUtil\create_slug;

/**
 * Class GroupService
 * @package App\Services
 */
class GroupService extends BaseService
{
    /**
     * @var DocumentRepository
     */
    protected $documentRepository;

    /**
     * @var DocumentRepository
     */
    protected $groupUserRepository;

    /**
     * @var DocumentRepository
     */
    protected $groupPosition;

    /**
     * @var RoleRepository
     */
    protected $roleRepository;

    /**
     * GroupService constructor.
     * @param GroupRepository $groupRepository
     * @param GroupValidator $groupValidator
     * @param DocumentRepository $documentRepository
     * @param GroupUserRepository $groupUserRepository
     * @param RoleRepository $roleRepository
     * @param GroupValidator $baseValidator
     * @param GroupPositionRepository $groupPosition
     */
    public function __construct(
        GroupRepository $groupRepository,
        GroupValidator $groupValidator,
        DocumentRepository $documentRepository,
        GroupUserRepository $groupUserRepository,
        RoleRepository $roleRepository,
        GroupValidator $baseValidator,
        GroupPositionRepository $groupPosition
    ) {
        $this->baseRepository = $groupRepository;
        $this->baseValidator = $baseValidator;
        $this->roleRepository = $roleRepository;
        $this->documentRepository = $documentRepository;
        $this->groupUserRepository = $groupUserRepository;
        $this->groupPosition = $groupPosition;
        $this->baseValidator = $groupValidator;
    }

    /**
     * @return mixed
     */
    public function getUserAllGroup()
    {
        $this->baseRepository->pushCriteria(new WhereCriteria('company_id', COMPANY_ID));
        return $this->baseRepository->findAllBy('owner_id', USER_ID, ['group_id', 'name']);
    }

    /**
     * @param $data
     * @return bool
     */
    public function addGroups($data)/*: bool*/
    {
        if (!$this->validate($this->baseValidator, $data, ['rule' => 'AddGroup'])) {
            return false;
        }

        $this->baseRepository->startTransaction();

        $roleLeader = $this->roleRepository->create([
            'role_type' => ConstPersonRole::LEADER,
            'permission' => ConstPersonPermission::EXECUTE
        ]);

        if (!$roleLeader) {
            $this->baseRepository->rollbackTransaction();
            return false;
        }

        $savedData = [
            'owner_id' => USER_ID,
            'company_id' => COMPANY_ID,
            'name' => $data['name'],
            'slug_url' => create_slug($data['name']),
            'type' => ConstGroupType::PRIVATE,
            'users_ids' => [
                [
                    'user_id' => USER_ID,
                    'role_id' => $roleLeader['role_id']
                ]
            ]
        ];

        $group = $this->baseRepository->saveAssociated($savedData, ['associated' => ['users']]);

        if (!$group) {
            $this->baseRepository->rollbackTransaction();
            return false;
        }

        $savePosition = $this->groupPosition->create([
            'user_id' => USER_ID,
            'company_id' => COMPANY_ID,
            'group_id' => $group,
            'current_position' => $this->getMaxCurrentPosition() + 1,
            'previous_position' => $this->getMaxCurrentPosition() + 1
        ]);

        if (!$savePosition) {
            $this->baseRepository->rollbackTransaction();
            return false;
        }

        if (!empty($data['users'])) {
            foreach ((array)$data['users'] as $user) {
                $roleUsers = $this->roleRepository->create([
                    'role_type' => $data['roleType'],
                    'permission' => ConstPersonPermission::READ
                ]);

                $groupUser = $this->groupUserRepository->create([
                    'group_id' => $group,
                    'user_id' => $user,
                    'role_id' => $roleUsers['role_id']
                ]);
            }

            if (!$groupUser) {
                $this->baseRepository->rollbackTransaction();
                return false;
            }
        }

        if (null !== $data['documents']) {

            if (!$this->documentRepository->updateBased(['group_id' => $group],
                ['document_id' => $data['documents']])) {

                $this->baseRepository->rollbackTransaction();
                return false;
            }

            $this->baseRepository->commitTransaction();
            return true;
        }

        $this->baseRepository->commitTransaction();
        return true;
    }

    /**
     * @param $id
     * @return bool
     */
    public function deleteGroup($id): bool
    {
        if (!$this->baseRepository->destroyAllByWhereIn('group_id', $id)) {
            return false;
        }

        return true;
    }

    /**
     * @param $groupName
     * @return null|string
     */
    public function rename($groupName): ?string
    {
        $slug = create_slug($groupName['name']);

        if (!$this->baseRepository->update(['name' => $groupName['name'], 'slug_url' => $slug],
            $groupName['groupId'])) {
            return null;
        }

        return $slug;
    }
}
