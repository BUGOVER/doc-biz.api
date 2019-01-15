<?php
declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Class DocumentUser
 *
 * @package App\Models
 * @property int $document_user_id
 * @property int|null $document_id
 * @property int|null $user_id
 * @property int|null $role_id
 * @property-read \App\Models\Role $role
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\DocumentUser whereDocumentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\DocumentUser whereDocumentUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\DocumentUser whereRoleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\DocumentUser whereUserId($value)
 * @mixin \Eloquent
 */
class DocumentUser extends BaseModel
{
    /**
     * @var bool
     */
    public $timestamps = false;
    /**
     * @var string
     */
    protected $table = 'document_user';
    /**
     * @var string
     */
    protected $primaryKey = 'document_user_id';
    /**
     * @var array
     */
    protected $fillable = [
        'document_id',
        'user_id',
        'role_id'
    ];

    protected $_relations = [
        'role'
    ];

    /**
     * @return HasOne
     */
    public function role(): HasOne
    {
        return $this->hasOne(Role::class, 'role_id', 'role_id');
    }
}
