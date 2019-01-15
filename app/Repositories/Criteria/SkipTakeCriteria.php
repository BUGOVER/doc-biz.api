<?php
/** @noinspection PhpUndefinedMethodInspection */
declare(strict_types=1);

namespace App\Repositories\Criteria;

use LaraRepo\Contracts\RepositoryInterface;
use LaraRepo\Criteria\Criteria;

/**
 * Class SkipTakeCriteria
 * @package App\Repositories\Criteria
 */
class SkipTakeCriteria extends Criteria
{
    /**
     * @var
     */
    protected $skip;

    /**
     * @var
     */
    protected $take;

    /**
     * SkipTakeCriteria constructor.
     * @param int $skip
     * @param int $take
     */
    public function __construct($skip, $take)
    {
        $this->skip = $skip;
        $this->take = $take;
    }


    /**
     * @param $modelQuery
     * @param RepositoryInterface $repository
     * @return mixed
     */
    public function apply($modelQuery, RepositoryInterface $repository)
    {
        return $modelQuery->skip($this->skip)->take($this->take);
    }
}
