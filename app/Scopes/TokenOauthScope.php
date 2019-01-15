<?php
declare(strict_types=1);


namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

/**
 * Class TokenOauthScop
 * @package App\Scopes
 */
class TokenOauthScope implements Scope
{
    /**
     * @param Builder $builder
     * @param Model $user
     */
    public function apply(Builder $builder, Model $user)
    {
        $builder
            ->where('company_id', '=', \Cache::get('_company_id_for_token_'))
            ->where('email', '=', \Cache::get('_auth_email_'));
    }
}
