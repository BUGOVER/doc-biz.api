<?php
declare(strict_types=1);

namespace App\Models;

/**
 * Class EmailTemplate
 *
 * @package App\Models
 * @property int $email_template_id
 * @property int|null $type
 * @property string|null $subject
 * @property string|null $body
 * @property string|null $description
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\EmailTemplate whereBody($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\EmailTemplate whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\EmailTemplate whereEmailTemplateId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\EmailTemplate whereSubject($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\EmailTemplate whereType($value)
 * @mixin \Eloquent
 */
class EmailTemplate extends BaseModel
{
    /**
     * @var string
     */
    protected $table = 'email_templates';

    /**
     * @var string
     */
    protected $primaryKey = 'email_template_id';

    /**
     * @var array
     */
    protected $fillable = [
        'type',
        'subject',
        'body',
        'description'
    ];
}
