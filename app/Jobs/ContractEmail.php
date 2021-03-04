<?php

namespace App\Jobs;

use App\Mail\ContractExipredMail;
use App\Models\Config;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Mail;

class ContractEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $contracts;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($contracts)
    {
        $this->contracts = $contracts;
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
        $email = new ContractExipredMail($this->contracts);
        Mail::to($to)->cc($cc_email, 'Contract Expired')->send($email);
    }
}
