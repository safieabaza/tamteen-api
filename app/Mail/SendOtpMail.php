<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;

class SendOtpMail extends Mailable
{
    public $code;

    public function __construct($code)
    {
        $this->code = $code;
    }

    public function build()
    {
        return $this->subject('Your OTP Code')
                    ->view('emails.otp');
    }
}