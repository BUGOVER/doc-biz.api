<?php
declare(strict_types=1);

namespace App\Providers;

use App\Repositories\Contracts\AdminPermissionRepositoryInterface;
use App\Repositories\Contracts\CompanyRepositoryInterface;
use App\Repositories\Contracts\CompanyUserRepositoryInterface;
use App\Repositories\Contracts\DocumentPositionRepositoryInterface;
use App\Repositories\Contracts\DocumentRepositoryInterface;
use App\Repositories\Contracts\DocumentUserRepositoryInterface;
use App\Repositories\Contracts\EmailTemplateRepositoryInterface;
use App\Repositories\Contracts\GroupPositionRepositoryInterface;
use App\Repositories\Contracts\GroupRepositoryInterface;
use App\Repositories\Contracts\GroupUserRepositoryInterface;
use App\Repositories\Contracts\OauthAccessTokenRepositoryInterface;
use App\Repositories\Contracts\RoleRepositoryInterface;
use App\Repositories\Contracts\UserInvitationRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Repositories\Eloquent\AdminPermissionRepository;
use App\Repositories\Eloquent\CompanyRepository;
use App\Repositories\Eloquent\CompanyUserRepository;
use App\Repositories\Eloquent\DocumentPositionRepository;
use App\Repositories\Eloquent\DocumentRepository;
use App\Repositories\Eloquent\DocumentUserRepository;
use App\Repositories\Eloquent\EmailTemplateRepository;
use App\Repositories\Eloquent\GroupPositionRepository;
use App\Repositories\Eloquent\GroupRepository;
use App\Repositories\Eloquent\GroupUserRepository;
use App\Repositories\Eloquent\OauthAccessTokenRepository;
use App\Repositories\Eloquent\RoleRepository;
use App\Repositories\Eloquent\UserInvitationRepository;
use App\Repositories\Eloquent\UserRepository;
use Illuminate\Support\ServiceProvider;

/**
 * Class RepositoryServiceProvider
 * @package App\Providers
 */
class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(AdminPermissionRepositoryInterface::class, AdminPermissionRepository::class);
        $this->app->bind(CompanyRepositoryInterface::class, CompanyRepository::class);
        $this->app->bind(CompanyUserRepositoryInterface::class, CompanyUserRepository::class);
        $this->app->bind(DocumentRepositoryInterface::class, DocumentRepository::class);
        $this->app->bind(GroupRepositoryInterface::class, GroupRepository::class);
        $this->app->bind(UserInvitationRepositoryInterface::class, UserInvitationRepository::class);
        $this->app->bind(EmailTemplateRepositoryInterface::class, EmailTemplateRepository::class);
        $this->app->bind(OauthAccessTokenRepositoryInterface::class, OauthAccessTokenRepository::class);
        $this->app->bind(RoleRepositoryInterface::class, RoleRepository::class);
        $this->app->bind(GroupUserRepositoryInterface::class, GroupUserRepository::class);
        $this->app->bind(DocumentUserRepositoryInterface::class, DocumentUserRepository::class);
        $this->app->bind(GroupPositionRepositoryInterface::class, GroupPositionRepository::class);
        $this->app->bind(DocumentPositionRepositoryInterface::class, DocumentPositionRepository::class);
    }
}
