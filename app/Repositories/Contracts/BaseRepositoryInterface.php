<?php
declare(strict_types=1);

namespace App\Repositories\Contracts;

use LaraRepo\Contracts\RepositoryInterface;

/**
 * Interface BaseRepositoryInterface
 * @package App\Repositories\Contracts
 */
interface BaseRepositoryInterface extends RepositoryInterface
{
    /**
     * @param $attribute
     * @param $value
     * @return mixed
     */
    public function destroyAllByWhereIn($attribute, $value);
}
