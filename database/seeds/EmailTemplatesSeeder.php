<?php
declare(strict_types=1);

use Illuminate\Database\Seeder;

/**
 * Class EmailTemplatesTableSeeder
 */
class EmailTemplatesSeeder extends Seeder
{

    /**
     * Auto generated seed file
     * @return void
     */
    public function run(): void
    {

        \DB::table('email_templates')->delete();

        \DB::table('email_templates')->insert([
            0 =>
                [
                    'email_template_id' => 1,
                    'type' => 1,
                    'description' => null,
                    'subject' => 'Welcome Email',
                    'body' => '<p>Dear [user_name],<br /><br /></p>
<p>Thank you very much for joining Our Company [company_name]. We hope you will find the tool convenient and useful.</p>
<p><br />Thanks again,<br /></p>'
                ],
            1 =>
                [
                    'email_template_id' => 2,
                    'type' => 2,
                    'description' => null,
                    'subject' => 'Confirm company key email',
                    'body' => '<p>Your key [key]</p>
<p>Thank you very much for joining Our Company [company_name].</p>
<p>To Key copy and paste viewed input</p>
<p><br />Thanks again,<br /></p>'
                ],
            2 =>
                [
                    'email_template_id' => 3,
                    'type' => 3,
                    'description' => null,
                    'subject' => 'user invitation email',
                    'body' => '<p>Dear [user_name] </p>
<p>Thank you very much for joining Our Company [company_name].</p>
<p>To confirm your email please click the link below

<a href="[invitation_link]">Confirm DocBiz invitation</a>
</p>
<p><br />Thanks again,<br /></p>'
                ],
            3 =>
                [
                    'email_template_id' => 4,
                    'type' => 4,
                    'description' => null,
                    'subject' => 'user reset password email',
                    'body' => '<p>Dear [user_name],<br /><br /></p>
Reset Password for [company_name]. Please click below to Redirect reset password page:</p>
<a href="[reset_password_link]">Reset Password</a>
</p>
<p><br />Thanks again,<br /></p>'
                ],
            4 =>
                [
                    'email_template_id' => 5,
                    'type' => 5,
                    'description' => null,
                    'subject' => 'Delete User In Company',
                    'body' => '<p>Dear [user_name] reputable [sender_name] has removed you from the company [company_name],<br /><br /></p>
please do not take it to heart remember that suicide is a sin continue to live on everything will be in chocolate
</p>
<p><br />Thanks again,<br /></p>'
                ]
        ]);

    }
}
