<?php
declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class UserInvitation
 *
 * @package App\Models
 * @property-read \App\Models\User $sender
 * @mixin \Eloquent
 */
class UserInvitation extends BaseModel
{
    /**
     * @var string
     */
    protected $primaryKey = 'user_invitation_id';

    /**
     * @var array
     */
    protected $fillable = [
        'user_id',
        'company_id',
        'status',
        'name',
        'email',
        'notes',
        'token',
        'permissions'
    ];

    /**
     * @var array
     */
    protected $_relations = [
        'sender'
    ];

    /**
     * @return BelongsTo
     */
    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }
}
