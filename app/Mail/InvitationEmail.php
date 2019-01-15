<?php
declare(strict_types=1);

namespace App\Mail;

use App\Config\ConstEmailTemplateType;
use App\Helpers\EmailSender;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * Class InvitationEmail
 * @package App\Mail
 */
class InvitationEmail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @var EmailSender
     */
    protected $body;

    /**
     * InvitationEmail constructor.
     * @param $params
     */
    public function __construct($params)
    {
        $this->body = new EmailSender(ConstEmailTemplateType::USER_INVITATION_EMAIL, $params);
    }

    /**
     * @return InvitationEmail
     */
    public function build()
    {
        return $this->view('vendor.emails.invitation-users')->with(['body' => $this->body]);
    }
}
