<?php
declare(strict_types=1);

namespace App\Mail;

use App\Config\ConstEmailTemplateType;
use App\Helpers\EmailSender;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * Class CompanyEmailVerified
 * @package App\Mail
 */
class CompanyEmailVerified extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @var EmailSender
     */
    protected $body;

    /**
     * @var
     */
    protected $key;

    /**
     * CompanyEmailVerified constructor.
     * @param $key
     */
    public function __construct(int $key)
    {
        $this->key = $key;
        $this->body = new EmailSender(ConstEmailTemplateType::CONFIRM_COMPANY_KEY_EMAIL, ['key' => $key]);
    }

    /**
     * @return CompanyEmailVerified
     */
    public function build(): CompanyEmailVerified
    {
        return $this->from(config('mail.from.address'))
            ->view('vendor.emails.company-email-verified')
            ->with(['body' => $this->body]);
    }
}
