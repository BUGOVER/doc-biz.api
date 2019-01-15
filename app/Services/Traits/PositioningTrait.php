<?php
/** @noinspection PhpUndefinedMethodInspection */
declare(strict_types=1);


namespace App\Services\Traits;

use App\Repositories\Contracts\DocumentPositionRepositoryInterface as DocumentPosition;
use App\Repositories\Contracts\GroupPositionRepositoryInterface as GroupPosition;
use App\Validators\PositioningValidator;
use LaraRepo\Criteria\Where\WhereCriteria;

/**
 * Trait PositioningTrait
 * @package App\Services\Traits
 */
trait PositioningTrait
{

    /**
     * @var DocumentPosition
     */
    protected $documentPosition;

    /**
     * @var GroupPosition
     */
    protected $groupPosition;

    /**
     * @var
     */
    protected $positionValidator;

    /**
     * PositioningTrait constructor.
     * @param DocumentPosition $documentPositionRepository
     * @param GroupPosition $groupPositionRepository
     * @param PositioningValidator $positioningValidator
     */
    public function __construct(
        DocumentPosition $documentPositionRepository,
        GroupPosition $groupPositionRepository,
        PositioningValidator $positioningValidator
    ) {
        $this->documentPosition = $documentPositionRepository;
        $this->groupPosition = $groupPositionRepository;
        $this->positionValidator = $positioningValidator;
    }

    /**
     * @param $group
     * @return mixed
     * @throws \Exception
     */
    public function groupStructure($group)
    {
        if (!$this->validate($this->positionValidator, $group, ['rule' => 'PositioningGroup'])) {
            return null;
        }

        if ($group['previousIndex'] > $group['newIndex']) {

            // -> Checked drag level is one step |>
            if (1 === abs($group['previousIndex'] - $group['newIndex'])) {
                return $this->filterByGroupOneStep($group, $group['previousIndex'], $group['newIndex']);
            }

            $averagePrevious = $group['previousIndex'];
            $averageCurrent = $group['newIndex'];
            $averageIndex = range(--$averagePrevious, ++$averageCurrent);
            $averageIndex[] = $group['newIndex'];

            return $this->filterByGroupManyStep($group, $averageIndex, $group['previousIndex'], $group['newIndex']);
        }

        if (1 === abs($group['previousIndex'] - $group['newIndex'])) {
            return $this->filterByGroupOneStep($group, $group['previousIndex'], $group['newIndex']);
        }

        $averagePrevious = $group['previousIndex'];
        $averageCurrent = $group['newIndex'];
        $averageIndex = range(++$averagePrevious, --$averageCurrent);
        $averageIndex[] = $group['newIndex'];

        return $this->filterByGroupManyStep($group, $averageIndex, $group['previousIndex'], $group['newIndex'],
            false);
    }

    /**
     * @param $document
     * @return bool|null
     * @throws \Exception
     */
    public function documentStructure($document): ?bool
    {
        if (!$this->validate($this->positionValidator, $document, ['rule' => 'PositioningDocument'])) {
            return null;
        }

        if ($document['previousIndex'] > $document['newIndex']) {

            // -> Checked drag level is one step |>
            if (1 === abs($document['previousIndex'] - $document['newIndex'])) {
                return $this->filterByDocumentOneStep($document, $document['previousIndex'], $document['newIndex']);
            }

            $averagePrevious = $document['previousIndex'];
            $averageCurrent = $document['newIndex'];
            $averageIndex = range(--$averagePrevious, ++$averageCurrent);
            $averageIndex[] = $document['newIndex'];

            return $this->filterByDocumentManyStep($document, $averageIndex, $document['previousIndex'],
                $document['newIndex']);
        }

        if (1 === abs($document['previousIndex'] - $document['newIndex'])) {
            return $this->filterByDocumentOneStep($document, $document['previousIndex'], $document['newIndex']);
        }

        $averagePrevious = $document['previousIndex'];
        $averageCurrent = $document['newIndex'];
        $averageIndex = range(++$averagePrevious, --$averageCurrent);
        $averageIndex[] = $document['newIndex'];

        return $this->filterByDocumentManyStep($document, $averageIndex, $document['previousIndex'],
            $document['newIndex'], false);
    }

    /**
     * @param array $data
     * @param int $previousIndex
     * @param int $currentIndex
     * @return bool|null
     */
    protected function filterByGroupOneStep(
        array $data,
        int $previousIndex,
        int $currentIndex
    ): ?bool {

        $this->groupPosition->resetCriteria();
        $this->groupPosition->pushCriteria(new WhereCriteria('user_id', USER_ID));
        $this->groupPosition->pushCriteria(new WhereCriteria('company_id', COMPANY_ID));
        $this->groupPosition->pushCriteria(new WhereCriteria('group_id', $data['groupId']));
        $this->groupPosition->pushCriteria(new WhereCriteria('current_position', $previousIndex));

        $draggedData = $this->groupPosition->first(['group_position_id']);

        $this->groupPosition->resetCriteria();
        $this->groupPosition->pushCriteria(new WhereCriteria('user_id', USER_ID));
        $this->groupPosition->pushCriteria(new WhereCriteria('company_id', COMPANY_ID));
        $this->groupPosition->pushCriteria(new WhereCriteria('current_position', $currentIndex));

        $secondDataUnDragGroup = $this->groupPosition->first(['group_position_id']);

        $this->groupPosition->startTransaction();

        if (!$secondDataUnDragGroup) { // -> Un Dragged Document |>

            $this->groupPosition->resetCriteria();

            $this->documentPosition->pushCriteria(new WhereCriteria('user_id', USER_ID));
            $this->documentPosition->pushCriteria(new WhereCriteria('company_id', COMPANY_ID));
            $this->documentPosition->pushCriteria(new WhereCriteria('current_position', $currentIndex));
            $secondDataUnDragDocument = $this->documentPosition->first(['document_position_id']);

            if (!$secondDataUnDragDocument) {
                $this->groupPosition->rollbackTransaction();
                return null;
            }

            $sd = $this->documentPosition->update([
                'current_position' => $previousIndex,
                'previous_position' => $currentIndex
            ], $secondDataUnDragDocument['document_position_id']);

            if (!$sd) {
                $this->groupPosition->rollbackTransaction();
                return null;
            }

            $dd = $this->updateCurrentGroup($currentIndex, $previousIndex, $draggedData['group_position_id']);

            if (!$dd) {
                $this->groupPosition->rollbackTransaction();
                return null;
            }

            $this->groupPosition->commitTransaction();
            return true;
        }

        // -> Un dragged Group |>

        $sd = $this->groupPosition->update([
            'current_position' => $previousIndex,
            'previous_position' => $currentIndex
        ], $secondDataUnDragGroup['group_position_id']);

        if (!$sd) {
            $this->groupPosition->rollbackTransaction();
            return null;
        }

        $this->groupPosition->resetCriteria();

        $dd = $this->updateCurrentGroup($currentIndex, $previousIndex, $draggedData['group_position_id']);

        if (!$dd) {
            $this->groupPosition->rollbackTransaction();
            return null;
        }

        $this->groupPosition->commitTransaction();
        return true;
    }

    /**
     * @param array $data
     * @param int $previousIndex
     * @param int $currentIndex
     * @return bool|null
     */
    protected function filterByDocumentOneStep(
        array $data,
        int $previousIndex,
        int $currentIndex
    ): ?bool {

        $this->documentPosition->resetCriteria();
        $this->documentPosition->pushCriteria(new WhereCriteria('user_id', USER_ID));
        $this->documentPosition->pushCriteria(new WhereCriteria('company_id', COMPANY_ID));
        $this->documentPosition->pushCriteria(new WhereCriteria('document_id', $data['documentId']));
        $this->documentPosition->pushCriteria(new WhereCriteria('current_position', $previousIndex));

        $draggedData = $this->documentPosition->first(['document_position_id']);

        $this->documentPosition->resetCriteria();
        $this->documentPosition->pushCriteria(new WhereCriteria('user_id', USER_ID));
        $this->documentPosition->pushCriteria(new WhereCriteria('company_id', COMPANY_ID));
        $this->documentPosition->pushCriteria(new WhereCriteria('current_position', $currentIndex));

        $secondDataUnDragDocument = $this->documentPosition->first(['document_position_id']);

        $this->documentPosition->startTransaction();

        if (!$secondDataUnDragDocument) { // -> Un Dragged Document |>

            $this->groupPosition->resetCriteria();

            $this->groupPosition->pushCriteria(new WhereCriteria('user_id', USER_ID));
            $this->groupPosition->pushCriteria(new WhereCriteria('company_id', COMPANY_ID));
            $this->groupPosition->pushCriteria(new WhereCriteria('current_position', $currentIndex));
            $secondDataUnDragGroup = $this->groupPosition->first(['group_position_id']);

            if (!$secondDataUnDragGroup) {
                $this->documentPosition->rollbackTransaction();
                return null;
            }

            $sd = $this->groupPosition->update([
                'current_position' => $previousIndex,
                'previous_position' => $currentIndex
            ], $secondDataUnDragGroup['group_position_id']);

            if (!$sd) {
                $this->documentPosition->rollbackTransaction();
                return null;
            }

            $dd = $this->updateCurrentDocument($currentIndex, $previousIndex, $draggedData['document_position_id']);

            if (!$dd) {
                $this->documentPosition->rollbackTransaction();
                return null;
            }

            $this->documentPosition->commitTransaction();
            return true;
        }

        // -> Un dragged Group |>

        $sd = $this->documentPosition->update([
            'current_position' => $previousIndex,
            'previous_position' => $currentIndex
        ], $secondDataUnDragDocument['document_position_id']);

        if (!$sd) {
            $this->groupPosition->rollbackTransaction();
            return null;
        }

        $this->documentPosition->resetCriteria();

        $dd = $this->updateCurrentDocument($currentIndex, $previousIndex, $draggedData['document_position_id']);

        if (!$dd) {
            $this->documentPosition->rollbackTransaction();
            return null;
        }

        $this->documentPosition->commitTransaction();
        return true;
    }

    /**
     * @param array $group
     * @param array $average
     * @param int $previousIndex
     * @param int $currentIndex
     * @param bool $counter
     * @return bool|null
     * @throws \Exception
     */
    protected function filterByGroupManyStep(
        array $group,
        array $average,
        int $previousIndex,
        int $currentIndex,
        bool $counter = true
    ): ?bool {
        $this->groupPosition->resetCriteria();

        /**
         *  -> Start Select id in db all dragged data
         */
        $this->groupPosition->pushCriteria(new WhereCriteria('user_id', USER_ID));
        $this->groupPosition->pushCriteria(new WhereCriteria('company_id', COMPANY_ID));
        $this->groupPosition->pushCriteria(new WhereCriteria('group_id', $group['groupId']));
        $this->groupPosition->pushCriteria(new WhereCriteria('current_position', $previousIndex));

        $draggedGroup = $this->groupPosition->first(['group_position_id']);

        $unDraggedGroup = [];
        foreach ($average as $avg) {

            $this->groupPosition->resetCriteria();

            $this->groupPosition->pushCriteria(new WhereCriteria('user_id', USER_ID));
            $this->groupPosition->pushCriteria(new WhereCriteria('company_id', COMPANY_ID));
            $this->groupPosition->pushCriteria(new WhereCriteria('current_position', $avg));

            $unDraggedGroup[] = $this->groupPosition->all(['group_position_id'])->toArray();
        }

        $unDraggedDocument = [];
        foreach ($average as $avg) {
            $this->documentPosition->resetCriteria();

            $this->documentPosition->pushCriteria(new WhereCriteria('user_id', USER_ID));
            $this->documentPosition->pushCriteria(new WhereCriteria('company_id', COMPANY_ID));
            $this->documentPosition->pushCriteria(new WhereCriteria('current_position', $avg));

            $unDraggedDocument[] = $this->documentPosition->all(['document_position_id'])->toArray();
        }

        $unDraggedGroupId = array_filter(array_map('array_filter', $unDraggedGroup));
        $unDraggedDocumentId = array_filter(array_map('array_filter', $unDraggedDocument));

        /**
         *  -> Start Update all dragged data
         */
        if (!empty($unDraggedGroupId) && !empty($unDraggedDocumentId)) {

            if (!$this->changePositionManyStepDuringGroup($unDraggedGroupId, $unDraggedDocumentId, $counter)) {
                $this->groupPosition->rollbackTransaction();
                return null;
            }

            if (!$this->updateCurrentGroup($currentIndex, $previousIndex, $draggedGroup['group_position_id'])) {
                $this->groupPosition->rollbackTransaction();
                return null;
            }

            $this->groupPosition->commitTransaction();
            return true;

        }

        if (!empty($unDraggedGroupId) && empty($unDraggedDocumentId)) {

            if (!$this->changePositionManyStepDuringGroup($unDraggedGroupId, [], $counter)) {
                $this->groupPosition->rollbackTransaction();
                return null;
            }

            if (!$this->updateCurrentGroup($currentIndex, $previousIndex, $draggedGroup['group_position_id'])) {
                $this->groupPosition->rollbackTransaction();
                return null;
            }

            $this->groupPosition->commitTransaction();
            return true;

        }

        if (empty($unDraggedGroupId) && !empty($unDraggedDocumentId)) {

            if (!$this->changePositionManyStepDuringGroup([], $unDraggedDocumentId, $counter)) {
                $this->groupPosition->rollbackTransaction();
                return null;
            }

            if (!$this->updateCurrentGroup($currentIndex, $previousIndex, $draggedGroup['group_position_id'])) {
                $this->groupPosition->rollbackTransaction();
                return null;
            }

            $this->groupPosition->commitTransaction();
            return true;
        }

        return null;
    }


    /**
     * @param array $document
     * @param array $average
     * @param int $previousIndex
     * @param int $currentIndex
     * @param bool $counter
     * @return bool|null
     * @throws \Exception
     */
    protected function filterByDocumentManyStep(
        array $document,
        array $average,
        int $previousIndex,
        int $currentIndex,
        bool $counter = true
    ): ?bool {
        $this->documentPosition->resetCriteria();

        /**
         * -> @TODO this code separate method |>
         *
         *  -> Start Select id in db all dragged data
         */
        $this->documentPosition->pushCriteria(new WhereCriteria('user_id', USER_ID));
        $this->documentPosition->pushCriteria(new WhereCriteria('company_id', COMPANY_ID));
        $this->documentPosition->pushCriteria(new WhereCriteria('document_id', $document['documentId']));
        $this->documentPosition->pushCriteria(new WhereCriteria('current_position', $previousIndex));

        $draggedDocument = $this->documentPosition->first(['document_position_id']);

        $unDraggedDocument = [];
        foreach ($average as $avg) {

            $this->documentPosition->resetCriteria();

            $this->documentPosition->pushCriteria(new WhereCriteria('user_id', USER_ID));
            $this->documentPosition->pushCriteria(new WhereCriteria('company_id', COMPANY_ID));
            $this->documentPosition->pushCriteria(new WhereCriteria('current_position', $avg));

            $unDraggedDocument[] = $this->documentPosition->all(['document_position_id'])->toArray();
        }

        $unDraggedGroup = [];
        foreach ($average as $avg) {
            $this->groupPosition->resetCriteria();

            $this->groupPosition->pushCriteria(new WhereCriteria('user_id', USER_ID));
            $this->groupPosition->pushCriteria(new WhereCriteria('company_id', COMPANY_ID));
            $this->groupPosition->pushCriteria(new WhereCriteria('current_position', $avg));

            $unDraggedGroup[] = $this->groupPosition->all(['group_position_id'])->toArray();
        }

        $unDraggedGroupId = array_filter(array_map('array_filter', $unDraggedGroup));
        $unDraggedDocumentId = array_filter(array_map('array_filter', $unDraggedDocument));
        /**
         *
         * -> @TODO this code separate method END |>
         */


        /**
         *  -> Start Update all dragged data
         */
        if (!empty($unDraggedGroupId) && !empty($unDraggedDocumentId)) {

            if (!$this->changePositionManyStepDuringGroup($unDraggedGroupId, $unDraggedDocumentId, $counter)) {
                $this->documentPosition->rollbackTransaction();
                return null;
            }

            if (!$this->updateCurrentDocument($currentIndex, $previousIndex,
                $draggedDocument['document_position_id'])) {
                $this->documentPosition->rollbackTransaction();
                return null;
            }

            $this->documentPosition->commitTransaction();
            return true;

        }

        if (!empty($unDraggedGroupId) && empty($unDraggedDocumentId)) {

            if (!$this->changePositionManyStepDuringGroup($unDraggedGroupId, [], $counter)) {
                $this->groupPosition->rollbackTransaction();
                return null;
            }

            if (!$this->updateCurrentDocument($currentIndex, $previousIndex,
                $draggedDocument['document_position_id'])) {
                $this->documentPosition->rollbackTransaction();
                return null;
            }

            $this->documentPosition->commitTransaction();
            return true;

        }

        if (empty($unDraggedGroupId) && !empty($unDraggedDocumentId)) {

            if (!$this->changePositionManyStepDuringGroup([], $unDraggedDocumentId, $counter)) {
                $this->groupPosition->rollbackTransaction();
                return null;
            }

            if (!$this->updateCurrentDocument($currentIndex, $previousIndex,
                $draggedDocument['document_position_id'])) {
                $this->documentPosition->rollbackTransaction();
                return null;
            }

            $this->documentPosition->commitTransaction();
            return true;
        }

        return null;
    }

    /**
     * @param array $dataGroup
     * @param array $dataDocument
     * @param bool $counter
     * @return bool|null
     * @throws \Exception
     */
    private function changePositionManyStepDuringGroup(
        array $dataGroup = [],
        array $dataDocument = [],
        bool $counter = true
    ): ?bool {

        $this->groupPosition->startTransaction();

        if (!empty($dataGroup) && !empty($dataDocument)) {

            $updateUnDraggedGroup = $this->updateGroupByGroup($dataGroup, $counter);

            if (!$updateUnDraggedGroup) {
                return null;
            }

            $updateUnDraggedDocument = $this->updateDocumentByDocument($dataDocument, $counter);

            if (!$updateUnDraggedDocument) {
                return null;
            }

            return true;
        }

        if (!empty($dataGroup) && empty($dataDocument)) {

            $updateUnDraggedGroup = $this->updateGroupByGroup($dataGroup, $counter);

            if (!$updateUnDraggedGroup) {
                return null;
            }

            return true;
        }

        if (empty($dataGroup) && !empty($dataDocument)) {

            $updateUnDraggedDocument = $this->updateDocumentByDocument($dataDocument, $counter);

            if (!$updateUnDraggedDocument) {
                return null;
            }

            return true;
        }

        return null;
    }

    /**
     * @param array $dataGroup
     * @param bool $counter
     * @return bool|null
     * @throws \Exception
     */
    protected function updateGroupByGroup(array $dataGroup, bool $counter = true): ?bool
    {
        $updateUnDraggedGroup = false;
        foreach ($dataGroup as $groups) {
            foreach ($groups as $value) {
                $this->groupPosition->resetCriteria();
                $updateUnDraggedGroup = $this->groupPosition->update([
                    'current_position' =>
                        $counter ? \DB::raw('current_position + 1') : \DB::raw('current_position - 1'),
                    'previous_position' =>
                        $counter ? \DB::raw('previous_position - 1') : \DB::raw('previous_position + 1')
                ], $value['group_position_id']);
            }
        }

        if (!$updateUnDraggedGroup) {
            return null;
        }

        return true;
    }

    /**
     * @param array $dataDocument
     * @param bool $counter
     * @return bool|null
     */
    public function updateDocumentByDocument(array $dataDocument, bool $counter = true): ?bool
    {
        $updateUnDraggedDocument = false;
        foreach ($dataDocument as $documents) {
            foreach ($documents as $value) {
                $this->documentPosition->resetCriteria();
                $updateUnDraggedDocument = $this->documentPosition->update([
                    'current_position' =>
                        $counter ? \DB::raw('current_position + 1') : \DB::raw('current_position - 1'),
                    'previous_position' =>
                        $counter ? \DB::raw('previous_position - 1') : \DB::raw('previous_position + 1')
                ], $value['document_position_id']);
            }
        }

        if (!$updateUnDraggedDocument) {
            return null;
        }

        return true;
    }

    /**
     * @param $currentIndex
     * @param $previousIndex
     * @param $draggedGroup
     * @return mixed
     */
    private function updateCurrentGroup($currentIndex, $previousIndex, $draggedGroup)
    {
        $this->groupPosition->resetCriteria();

        return $this->groupPosition->update([
            'current_position' => $currentIndex,
            'previous_position' => $previousIndex
        ], $draggedGroup);
    }

    /**
     * @param $currentIndex
     * @param $previousIndex
     * @param $draggedDocument
     * @return mixed
     */
    private function updateCurrentDocument($currentIndex, $previousIndex, $draggedDocument)
    {
        $this->documentPosition->resetCriteria();

        return $this->documentPosition->update([
            'current_position' => $currentIndex,
            'previous_position' => $previousIndex
        ], $draggedDocument);
    }
}
