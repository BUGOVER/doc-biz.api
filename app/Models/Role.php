<?php
declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use LaraModel\Models\LaraModel;

/**
 * Class Role
 *
 * @package App\Models
 * @property int $role_id
 * @property int $role_type
 * @property int $permission
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \App\Models\CompanyUser $company_user
 * @property-read \App\Models\DocumentUser $document_user
 * @property-read \App\Models\GroupUser $group_user
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Role whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Role wherePermission($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Role whereRoleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Role whereRoleType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Role whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Role extends LaraModel
{
    /**
     * @var string
     */
    protected $table = 'roles';

    /**
     * @var string
     */
    protected $primaryKey = 'role_id';

    /**
     * @var array
     */
    protected $fillable = [
        'role_type',
        'permission'
    ];

    /**
     * @var array
     */
    protected $_relations = [
        'company_user',
        'group_user',
        'document_user'
    ];

    /**
     * @return BelongsTo
     */
    public function company_user(): BelongsTo
    {
        return $this->belongsTo(CompanyUser::class, 'role_id', 'role_id');
    }

    /**
     * @return BelongsTo
     */
    public function group_user(): BelongsTo
    {
        return $this->belongsTo(GroupUser::class, 'role_id', 'role_id');
    }

    /**
     * @return BelongsTo
     */
    public function document_user(): BelongsTo
    {
        return $this->belongsTo(DocumentUser::class, 'role_id', 'role_id');
    }
}
