<?php
/** @noinspection PhpUndefinedMethodInspection */
declare(strict_types=1);

namespace App\Repositories\Criteria;

use LaraRepo\Contracts\RepositoryInterface;
use LaraRepo\Criteria\Criteria;

/**
 * Class WithCountCriteria
 * @package App\Repositories\Criteria
 */
class WherePivotNonEqualCriteria extends Criteria
{
    /**
     * @var
     */
    protected $column;
    /**
     * @var
     */
    protected $columns;
    /**
     * @var
     */
    protected $relationName;

    /**
     * WhereNullRelationCriteria constructor.
     * @param string $relationName
     * @param string $nullColumn
     * @param $columns
     * @param $andWhere
     */
    public function __construct(string $relationName, $equals)
    {
        $this->relationName = $relationName;
        $this->columns = $equals;
    }

    /**
     * @param $modelQuery
     * @param RepositoryInterface $repository
     * @return mixed
     */
    public function apply($modelQuery, RepositoryInterface $repository)
    {
        return $modelQuery->with([
            $this->relationName => function ($query) {
                $query->where($this->column)->select($this->columns);
            }
        ]);
    }
}
