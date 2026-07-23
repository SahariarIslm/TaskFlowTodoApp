<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendOtpMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(public string $otp)
    {
        //
    }

    public function build(): self
    {
        return $this->subject('Your TaskFlow Verification Code')
            ->html("<h2>Your OTP code is: <strong>{$this->otp}</strong></h2><p>This code expires in 10 minutes.</p>");
    }
}
