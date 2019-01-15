<?php
declare(strict_types=1);

namespace App\Models;

/**
 * Class DcoumentPosition
 * @package App\Models
 */
class DocumentPosition extends BaseModel
{
    /**
     * @var string
     */
    protected $table = 'sm_document_positions';

    /**
     * @var string
     */
    protected $primaryKey = 'document_position_id';

    /**
     * @var array
     */
    protected $fillable = [
        'user_id',
        'document_id',
        'company_id',
        'current_position',
        'previous_position',
    ];

    /**
     * @var array
     */
    protected $_relations = [
        'user',
        'document'
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
    public function document()
    {
        $this->belongsTo(Document::class, 'document_id', 'document_id');
    }
}
