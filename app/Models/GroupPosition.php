<?php
declare(strict_types=1);

namespace App\Models;

/**
 * Class GroupPosition
 * @package App\Models
 */
class GroupPosition extends BaseModel
{
    /**
     * @var string
     */
    protected $table = 'sm_group_positions';

    /**
     * @var string
     */
    protected $primaryKey = 'group_position_id';

    /**
     * @var array
     */
    protected $fillable = [
        'user_id',
        'group_id',
        'company_id',
        'current_position',
        'previous_position',
    ];

    /**
     * @var array
     */
    protected $_relations = [
        'user',
        'group'
    ];

    /**
     *
     */
    public function user()
    {
        $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    /**
     *
     */
    public function group()
    {
        $this->belongsTo(Group::class, 'group_id', 'group_id');
    }
}
