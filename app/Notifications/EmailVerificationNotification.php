<?php

namespace App\Notifications;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\URL;
use App\Mail\VerificationEmail;
use Auth;
use App\Models\User;

class EmailVerificationNotification extends Notification
{
    use Queueable;

    public function __construct()
    {
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $notifiable->verification_code = encrypt($notifiable->id);
        $notifiable->save();

        $array['view'] = 'emails.verify';
        $array['subject'] = 'Email Verification';
        $array['content'] = 'You have registered an account on eCommerceApp, before being able to use your account you need to verify that this is your email address by clicking the button below.';
        $array['link'] = route('verification.confirmation', $notifiable->verification_code);

        return (new MailMessage)
            ->view('emails.verify', ['array' => $array])
            ->subject('Email Verification - '.env('APP_NAME'));
    }

    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
