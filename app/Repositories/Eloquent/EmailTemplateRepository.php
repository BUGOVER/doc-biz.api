<?php
declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\EmailTemplate;
use App\Repositories\Contracts\EmailTemplateRepositoryInterface;

/**
 * Class EmailTemplateRepository
 * @package App\Repositories\Eloquent
 */
class EmailTemplateRepository extends BaseRepository implements EmailTemplateRepositoryInterface
{
    /**
     * Specify Model class name
     *
     * @return mixed
     */
    public function modelClass()
    {
        return EmailTemplate::class;
    }
}
