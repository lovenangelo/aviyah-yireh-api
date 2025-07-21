<?php

namespace App\Notifications;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Config;
use Illuminate\Notifications\Messages\MailMessage;

class VerifyEmail extends \Illuminate\Auth\Notifications\VerifyEmail
{
    protected $useApiRoute;

    protected function verificationUrl($notifiable)
    {
        $verificationExpireTime = Config::get(
            'auth.verification.expire',
            Config::get('auth.passwords.users.expire', 60)
        );

        return URL::temporarySignedRoute(
            'api.verification.verify',
            Carbon::now()->addMinutes($verificationExpireTime),
            [
                'id' => $notifiable->getKey(),
                'hash' => sha1($notifiable->getEmailForVerification()),
            ]
        );
    }
}
