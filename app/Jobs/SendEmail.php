<?php

namespace App\Jobs;

use App\Mail\DocumentExpiredMail;
use App\Models\Config;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Mail;

class SendEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $documents;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($documents)
    {
        $this->documents = $documents;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $push_email = Config::where('option', 'email_push');
        $cc_email = Config::where('option', 'company_email');
        $to = explode(',', $push_email);
        $email = new DocumentExpiredMail($this->documents);
        Mail::to($to)->cc($cc_email, 'Document Expired')->send($email);
    }
}
