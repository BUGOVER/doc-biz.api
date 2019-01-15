<?php
declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\OauthAccessToken;
use App\Repositories\Contracts\OauthAccessTokenRepositoryInterface;

/**
 * Class OauthAccessTokenRepository
 * @package App\Repositories\Eloquent
 */
class OauthAccessTokenRepository extends BaseRepository implements OauthAccessTokenRepositoryInterface
{
    /**
     * @return mixed|string
     */
    public function modelClass()
    {
        return OauthAccessToken::class;
    }
}
