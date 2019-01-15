<?php
declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\Document;
use App\Repositories\Contracts\DocumentRepositoryInterface;

/**
 * Class DocumentRepository
 * @package App\Repositories\Eloquent
 */
class DocumentRepository extends BaseRepository implements DocumentRepositoryInterface
{
    /**
     * @return mixed|string
     */
    public function modelClass()
    {
        return Document::class;
    }
}
