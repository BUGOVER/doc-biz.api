<?php
declare(strict_types=1);

namespace App\Mail;

use App\Config\ConstEmailTemplateType;
use App\Helpers\EmailSender;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * Class InvitationWelcome
 * @package App\Mail
 */
class InvitationWelcome extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @var
     */
    protected $body;

    /**
     * InvitationWelcome constructor.
     * @param $userName
     * @param $companyName
     */
    public function __construct($userName, $companyName)
    {
        $this->body = new EmailSender(ConstEmailTemplateType::WELCOME_EMAIL,
            [
                'user_name' => $userName,
                'company_name' => $companyName
            ]);
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('vendor.emails.welcome')->with(['body' => $this->body]);
    }
}
