<?php
declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use LaraModel\Models\LaraAuthenticatable;
use Laravel\Passport\HasApiTokens;

/**
 * Class User
 *
 * @package App\Models
 * @property int $user_id
 * @property int|null $inviting_user_id
 * @property string $name
 * @property string $email
 * @property string $password
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\OauthAccessToken[] $auth_access_tokens
 * @property-read \Illuminate\Database\Eloquent\Collection|\Laravel\Passport\Client[] $clients
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Company[] $companies
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Company[] $companies_leader
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Document[] $document_owner
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Document[] $documents
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Group[] $group_owner
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Group[] $groups
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read \Illuminate\Database\Eloquent\Collection|\Laravel\Passport\Token[] $tokens
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\UserInvitation[] $user_invitation
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereInvitingUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereUserId($value)
 * @mixin \Eloquent
 */
class User extends LaraAuthenticatable
{
    use HasApiTokens;

    /**
     * @var string
     */
    protected $primaryKey = 'user_id';

    /**
     * @var array
     */
    protected $fillable = [
        'user_id',
        'inviting_user_id',
        'company_id',
        'name',
        'email',
        'password'
    ];

    /**
     * @var array
     */
    protected $hidden = [
//        'password'
    ];

    /**
     * @var array
     */
    protected $_relations = [
        'companies_leader',
        'companies',
        'groups',
        'auth_access_tokens',
        'user_invitation',
        'group_owner',
        'document_owner',
        'documents',
        'documents_position',
        'groups_position'
    ];


    /**
     * @return HasMany
     */
    public function companies_leader(): HasMany
    {
        return $this->hasMany(Company::class, 'leader_id', 'user_id');
    }

    /**
     * @return HasMany
     */
    public function auth_access_tokens(): HasMany
    {
        return $this->hasMany(OauthAccessToken::class, 'user_id');
    }

    /**
     * @return HasMany
     */
    public function user_invitation(): HasMany
    {
        return $this->hasMany(UserInvitation::class, 'sender_id');
    }

    /**
     * @return HasMany
     */
    public function group_owner(): HasMany
    {
        return $this->hasMany(Group::class, 'owner_id');
    }

    /**
     * @return HasMany
     */
    public function document_owner(): HasMany
    {
        return $this->hasMany(Document::class, 'owner_id');
    }

    /**
     * @return BelongsToMany
     */
    public function groups(): BelongsToMany
    {
        return $this->belongsToMany(Group::class, 'group_user', 'user_id', 'group_id')
            ->withPivot('role_id');
    }

    /**
     * @return BelongsToMany
     */
    public function documents(): BelongsToMany
    {
        return $this->belongsToMany(Document::class, 'document_user', 'user_id', 'document_id')
            ->withPivot('role_id');
    }

    /**
     * @return BelongsToMany
     */
    public function companies(): BelongsToMany
    {
        return $this->belongsToMany(Company::class, 'company_user', 'user_id', 'company_id')
            ->withPivot('role_id');
    }

    /**
     * @return HasMany
     */
    public function documents_position(): HasMany
    {
        return $this->hasMany(DocumentPosition::class, 'user_id', 'user_id');
    }

    /**
     * @return HasMany
     */
    public function groups_position(): HasMany
    {
        return $this->hasMany(GroupPosition::class, 'user_id', 'user_id');
    }
}
