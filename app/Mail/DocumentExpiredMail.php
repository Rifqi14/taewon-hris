<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class DocumentExpiredMail extends Mailable
{
    use Queueable, SerializesModels;
    protected $documents;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($documents)
    {
        $this->documents = $documents;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $documents = $this->documents;
        return $this->subject('Document Expired')->view('emails.document', compact('documents'));
    }
}
