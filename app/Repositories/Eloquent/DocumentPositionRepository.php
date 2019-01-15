<?php
declare(strict_types=1);


namespace App\Repositories\Eloquent;


use App\Models\DocumentPosition;
use App\Repositories\Contracts\DocumentPositionRepositoryInterface;

/**
 * Class DocumentPositionRepository
 * @package App\Repositories\Eloquent
 */
class DocumentPositionRepository extends BaseRepository implements DocumentPositionRepositoryInterface
{
    /**
     * Specify Model class name
     *
     * @return mixed
     */
    public function modelClass()
    {
        return DocumentPosition::class;
    }
}
