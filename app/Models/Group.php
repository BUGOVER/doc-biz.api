<?php
declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Class Group
 *
 * @package App\Models
 * @property int $group_id
 * @property int|null $owner_id
 * @property int|null $company_id
 * @property string $name
 * @property string|null $slug_url
 * @property int $type
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \App\Models\Company|null $company
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Document[] $documents
 * @property-read \App\Models\User|null $owner
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\User[] $users
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Group whereCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Group whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Group whereGroupId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Group whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Group whereOwnerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Group whereSlugUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Group whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Group whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Group extends BaseModel
{
    /**
     * @var string
     */
    protected $primaryKey = 'group_id';

    /**
     * @var array
     */
    protected $fillable = [
        'owner_id',
        'company_id',
        'name',
        'type',
        'slug_url'
    ];

    /**
     * @var array
     */
    protected $_relations = [
        'users',
        'owner',
        'documents',
        'company',
        'positions',
        'position',
    ];

//    protected $hidden = ['pivot'];

    /**
     * @return BelongsToMany
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'group_user', 'group_id', 'user_id')
            ->withPivot('role_id');
    }

    /**
     * @return BelongsTo
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * @return BelongsTo
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id', 'company_id');
    }

    /**
     * @return HasMany
     */
    public function documents(): HasMany
    {
        return $this->hasMany(Document::class, 'group_id', 'group_id');
    }

    /**
     * @return HasMany
     */
    public function positions(): HasMany
    {
        return $this->hasMany(GroupPosition::class, 'group_id', 'group_id');
    }

    /**
     * @return HasOne
     */
    public function position(): HasOne
    {
        return $this->hasOne(GroupPosition::class, 'group_id', 'group_id');
    }
}
