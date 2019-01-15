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
class WithCountCriteria extends Criteria
{
    /**
     * @var
     */
    protected $column;
    /**
     * @var
     */
    protected $value;
    /**
     * @var
     */
    protected $relationName;
    protected $andWhere;
    protected $select;

    /**
     * WithCountCriteria constructor.
     * @param string $relationName
     * @param string $column
     * @param $value
     * @param array $andWhere
     * @param array $select
     */
    public function __construct(string $relationName, string $column, $value, array $andWhere = [], $select = [])
    {
        $this->relationName = $relationName;
        $this->column = $column;
        $this->value = $value;
        $this->andWhere = $andWhere;
        $this->select = $select;
    }

    /**
     * @param $modelQuery
     * @param RepositoryInterface $repository
     * @return mixed
     */
    public function apply($modelQuery, RepositoryInterface $repository)
    {
        if (!empty($this->andWhere)) {
            return $modelQuery->withCount([
                $this->relationName => function ($query) {
                    $query->where($this->column, '=', $this->value)->where($this->andWhere);
                }
            ]);
        }

        if (!empty($this->select) && !empty($this->andWhere)) {
            return $modelQuery->withCount([
                $this->relationName => function ($query) {
                    $query->where($this->column, '=', $this->value)->where($this->andWhere)->select($this->select);
                }
            ]);
        }

        return $modelQuery->withCount([
            $this->relationName => function ($query) {
                $query->where($this->column, '=', $this->value);
            }
        ]);
    }
}
