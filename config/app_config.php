<?php
declare(strict_types=1);

return [
    'email_confirm_time' => 120, //Minute
    'invitation_link_period' => 24, //Hour
    'reset_password_link_period' => 480, //Minutes
    'invitation_accepted_redirection_link' => env('APP_URL') . 'sign-in-i-data/', // Todo Invite after redirect link
    'reset_password_redirection_link' => env('APP_URL') . 'reset-password/', // Todo Reset Password redirect link
];
