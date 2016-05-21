<?php

namespace App\Http\Helpers;

use Mail;

class EmailUtilities
{
    public static function sendEmail(array $emailData, array $emailConfig)
    {
        return Mail::send('email_template', $emailData, function($message) use ($emailConfig) {
            $message->to($emailConfig['to'])->subject($emailConfig['subject']);
        });
    }
}
