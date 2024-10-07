<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RecoverPasswordEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $razao_social;
    public $token;

    /**
     * Create a new message instance.
     */
    public function __construct($razao_social, $token)
    {
        $this->razao_social = $razao_social;
        $this->token = $token;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->view('emails.passwordRecovery')
                    ->with([
                        'razao_social' => $this->razao_social,
                        'token' => $this->token,
                    ]);
    }
}