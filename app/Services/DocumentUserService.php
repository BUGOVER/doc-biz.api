<?php
declare(strict_types=1);

namespace App\Services;

use App\Config\ConstPersonPermission;
use App\Repositories\Contracts\DocumentUserRepositoryInterface;
use App\Repositories\Contracts\RoleRepositoryInterface;
use App\Validators\DocumentUSerValidator;
use LaraRepo\Criteria\Where\WhereCriteria;
use LaraRepo\Criteria\With\RelationCriteria;

/**
 * Class DocumentUserService
 * @package App\Services
 */
class DocumentUserService extends BaseService
{
    /**
     * @var
     */
    protected $roleRepository;

    /**
     * DocumentUserService constructor.
     * @param DocumentUserRepositoryInterface $documentUserRepository
     * @param DocumentUSerValidator $documentUSerValidator
     * @param RoleRepositoryInterface $roleRepository
     */
    public function __construct(
        DocumentUserRepositoryInterface $documentUserRepository,
        DocumentUSerValidator $documentUSerValidator,
        RoleRepositoryInterface $roleRepository
    ) {
        $this->baseRepository = $documentUserRepository;
        $this->roleRepository = $roleRepository;
        $this->baseValidator = $documentUSerValidator;
    }


    /**
     * @param $userId
     * @return mixed
     */
    public function hasUserDocument($userId)
    {
        $related = [
            'role' => [
                'columns' => [
                    'role_id',
                    'role_type'
                ]
            ]
        ];

        $this->baseRepository->pushCriteria(new RelationCriteria($related));
        $roles = $this->baseRepository->findAllBy('user_id', $userId, ['user_id', 'document_id', 'role_id']);

        if (!$roles) {
            return null;
        }

        $returnData = [];
        foreach ($roles as $role) {
            $returnData[] = ['document_id' => $role['document_id'], 'role_type' => $role['role']['role_type']];
        }

        return $returnData;
    }

    /**
     * @param $data
     * @return mixed
     */
    public function getPermission($data)
    {
        $this->baseRepository->pushCriteria(new WhereCriteria('document_id', $data['document_id']));
        $role = $this->baseRepository->findBy('user_id', $data['user_id'], 'role_id');

        $related = [
            'role' => [
                'columns' => [
                    'role_id',
                    'role_type',
                    'permission'
                ],
                'where' => ['role_id' => $role['role_id']]
            ]
        ];

        $this->baseRepository->pushCriteria(new RelationCriteria($related));
        return $this->baseRepository->findBy('user_id', $data['user_id']);
    }

    /**
     * @param $data
     * @return bool
     */
    public function editRole($data): bool
    {
        $this->baseRepository->pushCriteria(new WhereCriteria('document_id', $data['documentId']));
        $role = $this->baseRepository->findBy('user_id', $data['userId'], 'role_id');

        if (!$this->roleRepository->update(['role_type' => $data['roleType']], $role['role_id'])) {
            return false;
        }

        return true;
    }

    /**
     * @param $data
     * @return bool
     */
    public function addUserDocument($data): bool
    {
        $this->baseRepository->startTransaction();

        $role = $this->roleRepository->create([
            'role_type' => $data['roleType'],
            'permission' => ConstPersonPermission::READ
        ]);

        if (!$role) {
            $this->baseRepository->rollbackTransaction();
            return false;
        }

        $document = $this->baseRepository->create([
            'document_id' => $data['documentId'],
            'user_id' => $data['userId'],
            'role_id' => $role['role_id']
        ]);

        if (!$document) {
            $this->baseRepository->rollbackTransaction();
            return false;
        }

        $this->baseRepository->commitTransaction();
        return true;
    }
}
