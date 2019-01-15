<?php
declare(strict_types=1);

namespace App\Models;

/**
 * Class GroupUser
 *
 * @package App\Models
 * @property int $group_user_id
 * @property int $group_id
 * @property int $user_id
 * @property int|null $role_id
 * @property-read \App\Models\Role $role
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GroupUser whereGroupId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GroupUser whereGroupUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GroupUser whereRoleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GroupUser whereUserId($value)
 * @mixin \Eloquent
 */
class GroupUser extends BaseModel
{
    /**
     * @var bool
     */
    public $timestamps = false;
    /**
     * @var string
     */
    protected $table = 'group_user';
    /**
     * @var string
     */
    protected $primaryKey = 'group_user_id';
    /**
     * @var array
     */
    protected $fillable = [
        'user_id',
        'group_id',
        'role_id'
    ];

    protected $_relations = [
        'role'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function role(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Role::class, 'role_id', 'role_id');
    }
}
