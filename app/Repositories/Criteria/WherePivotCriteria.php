<?php
declare(strict_types=1);

namespace App\Repositories\Criteria;

use LaraRepo\Contracts\RepositoryInterface;
use LaraRepo\Criteria\Criteria;

/**
 * Class WherePivotCriteria
 * @package App\Repositories\Criteria
 */
class WherePivotCriteria extends Criteria
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
     * WherePivotCriteria constructor.
     * @param $column
     * @param $value
     */
    public function __construct($column, $value)
    {
        $this->column = $column;
        $this->value = $value;
    }

    /**
     * @param $modelQuery
     * @param RepositoryInterface $repository
     * @return mixed
     */
    public function apply($modelQuery, RepositoryInterface $repository)
    {
        return $modelQuery->wherePivot($this->column, $this->value);
    }
}
