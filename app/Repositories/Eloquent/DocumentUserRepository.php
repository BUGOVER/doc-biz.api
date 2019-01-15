<?php
declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\DocumentUser;
use App\Repositories\Contracts\DocumentUserRepositoryInterface;

/**
 * Class DocumentUserRepository
 * @package App\Repositories\Eloquent
 */
class DocumentUserRepository extends BaseRepository implements DocumentUserRepositoryInterface
{
    /**
     * @return mixed|string
     */
    public function modelClass()
    {
        return DocumentUser::class;
    }
}
