<?php

declare(strict_types=1);

return [
    'app_url_front' => env('APP_URL'),

    /*
    |--------------------------------------------------------------------------
    | max_emails_forgot_access_code_a_day
    |--------------------------------------------------------------------------
    | To prevent too much emails sent (= fees), an user cannot ask an email
    | to receive too much reset password links a day
    */
    'max_emails_forgot_password_a_day' => (int) env('MAX_EMAIL_FORGOT_PASSWORD_A_DAY', 3),
    'max_emails_resent_email_invitation_a_day' => (int) env('MAX_EMAIL_RESENT_EMAIL_INVITATION_A_DAY', 3),

    /*
    |--------------------------------------------------------------------------
    | delay_validity_token_reset_password
    |--------------------------------------------------------------------------
    | Delay validity (in minutes) of a token to reset a password
    */
    'delay_validity_token_reset_password' => (int) env('DELAY_VALIDITY_TOKEN_RESET_PASSWORD', 30),

    /*
    |--------------------------------------------------------------------------
    | max_email_contact_owner_a_day
    |--------------------------------------------------------------------------
    | qty max emails an user can send to contact the owner of a classified ad (by day)
    */
    'max_email_contact_owner_a_day' => (int) env('MAX_EMAIL_CONTACT_OWNER_A_DAY', 10),

];
