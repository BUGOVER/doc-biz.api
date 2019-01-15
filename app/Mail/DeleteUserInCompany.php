<?php
declare(strict_types=1);

namespace App\Mail;

use App\Config\ConstEmailTemplateType;
use App\Helpers\EmailSender;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * Class DeleteUserInCompany
 * @package App\Mail
 */
class DeleteUserInCompany extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @var EmailSender
     */
    public $body;

    /**
     * DeleteUserInCompany constructor.
     * @param $params
     */
    public function __construct($params)
    {
        $this->body = new EmailSender(ConstEmailTemplateType::USER_DELETED_IN_COMPANY, $params);
    }

    /**
     * @return DeleteUserInCompany
     */
    public function build()
    {
        return $this->view('vendor.emails.welcome')->with(['body' => $this->body]);
    }
}
