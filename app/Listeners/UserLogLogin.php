<?php

namespace App\Listeners;

use App\Models\Log;
use Illuminate\Auth\Events\Login;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class UserLogLogin
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  Login  $event
     * @return void
     */
    public function handle(Login $event)
    {
        $user = $event->user;
        $user->last_login = new \DateTime;
        $user->save();
        
        $logs = new Log();
        $logs->user_id = $user->id;
        $logs->ip_address = $_SERVER['REMOTE_ADDR'];
        $logs->device = $_SERVER['HTTP_USER_AGENT'];
        $logs->last_login = new \DateTime;
        $logs->save();
    }
}
