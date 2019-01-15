<?php
declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Class Document
 *
 * @package App\Models
 * @property int $document_id
 * @property int $owner_id
 * @property int|null $group_id
 * @property int|null $company_id
 * @property string $name
 * @property string|null $slug_url
 * @property string $description
 * @property string|null $content
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \App\Models\Company|null $company
 * @property-read \App\Models\Group|null $group
 * @property-read \App\Models\User $owner
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\User[] $users
 * @property mixed user_id
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Document whereCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Document whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Document whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Document whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Document whereDocumentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Document whereGroupId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Document whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Document whereOwnerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Document whereSlugUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Document whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Document extends BaseModel
{
    /**
     * @var string
     */
    protected $table = 'documents';

    /**
     * @var string
     */
    protected $primaryKey = 'document_id';

    /**
     * @var array
     */
    protected $fillable = [
        'document_id',
        'owner_id',
        'group_id',
        'company_id',
        'name',
        'description',
        'content',
        'slug_url'
    ];

    /**
     * @var array
     */
    protected $_relations = [
        'owner',
        'group',
        'company',
        'users',
        'positions',
        'position',
    ];

//    protected $hidden = ['pivot'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id', 'user_id');
    }

    /**
     * @return BelongsToMany
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'document_user', 'document_id', 'user_id')
            ->withPivot('role_id');
    }

    /**
     * @return BelongsTo
     */
    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class, 'group_id', 'group_id');
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
    public function positions(): HasMany
    {
        return $this->hasMany(DocumentPosition::class, 'document_id', 'document_id');
    }

    /**
     * @return HasOne
     */
    public function position(): HasOne
    {
        return $this->hasOne(DocumentPosition::class, 'document_id', 'document_id');
    }
}
