<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ContractExipredMail extends Mailable
{
    use Queueable, SerializesModels;
    protected $contracts;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($contracts)
    {
        $this->contracts = $contracts;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $contracts = $this->contracts;
        return $this->subject('Contract Expired')->view('emails.contract', compact('contracts'));
    }
}
