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
class WhereNullRelationCriteria extends Criteria
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
     * @var
     */
    protected $andWhere;

    /**
     * WhereNullRelationCriteria constructor.
     * @param string $relationName
     * @param string $nullColumn
     * @param $columns
     * @param $andWhere
     */
    public function __construct(string $relationName, string $nullColumn, $columns, $andWhere)
    {
        $this->relationName = $relationName;
        $this->column = $nullColumn;
        $this->columns = $columns;
        $this->andWhere = $andWhere;
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
                $query->whereNull($this->column)->where($this->andWhere)->select($this->columns);
            }
        ]);
    }
}
