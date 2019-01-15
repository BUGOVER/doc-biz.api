<?php
declare(strict_types=1);

namespace App\Mail;

use App\Config\ConstEmailTemplateType;
use App\Helpers\EmailSender;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * Class ResetPassword
 * @package App\Mail
 */
class ResetPassword extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @var EmailSender
     */
    protected $body;

    /**
     * ResetPassword constructor.
     * @param $params
     */
    public function __construct($params)
    {
        $this->body = new EmailSender(ConstEmailTemplateType::USER_RESET_PASSWORD_EMAIL, $params);
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
