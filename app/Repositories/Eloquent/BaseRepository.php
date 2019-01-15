<?php
declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Repositories\Contracts\BaseRepositoryInterface;
use LaraRepo\Criteria\Where\WhereInCriteria;
use LaraRepo\Eloquent\AbstractRepository;

/**
 * Class BaseRepository
 * @package App\Repositories\Eloquent
 */
abstract class BaseRepository extends AbstractRepository implements BaseRepositoryInterface
{
    /**
     * @param $attribute
     * @param $value
     * @return mixed
     */
    public function destroyAllByWhereIn($attribute, $value)
    {
        $this->pushCriteria(new WhereInCriteria($attribute, $value));
        $this->applyCriteria();
        return $this->modelQuery->delete();
    }
}
