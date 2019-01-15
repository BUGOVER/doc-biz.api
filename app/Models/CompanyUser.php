<?php
declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use LaraModel\Models\LaraModel;

/**
 * Class CompanyUser
 *
 * @package App\Models
 * @property int $company_user_id
 * @property int|null $company_id
 * @property int|null $user_id
 * @property int|null $role_id
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Company[] $companies
 * @property-read \App\Models\Role $role
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\User[] $users
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CompanyUser whereCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CompanyUser whereCompanyUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CompanyUser whereRoleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CompanyUser whereUserId($value)
 * @mixin \Eloquent
 */
class CompanyUser extends LaraModel
{
    /**
     * @var bool
     */
    public $timestamps = false;
    /**
     * @var string
     */
    protected $table = 'company_user';
    /**
     * @var string
     */
    protected $primaryKey = 'company_user_id';
    /**
     * @var array
     */
    protected $fillable = [
        'company_id',
        'user_id',
        'role_id'
    ];

    /**
     * @var array
     */
    protected $_relations = [
        'role',
        'companies',
        'users'
    ];

    /**
     * @return HasOne
     */
    public function role(): HasOne
    {
        return $this->hasOne(Role::class, 'role_id', 'role_id');
    }

    /**
     * @return BelongsToMany
     */
    public function companies(): BelongsToMany
    {
        return $this->belongsToMany(Company::class, 'companies', 'company_id', 'company_id');
    }

    /**
     * @return BelongsToMany
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'users', 'user_id', 'user_id');
    }
}
