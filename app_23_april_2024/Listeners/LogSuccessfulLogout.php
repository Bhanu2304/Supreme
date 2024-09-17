<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Logout;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\LoginActivity;

class LogSuccessfulLogout
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
    public function handle(Logout $event)
    {
        LoginActivity::create([
        'user_id'       =>  $event->user->id,
        'user_agent'    =>  \Illuminate\Support\Facades\Request::header('User-Agent'),
        'ip_address'    =>  \Illuminate\Support\Facades\Request::ip(),
            'activity_perform' =>'Logout'
    ]);
    }
}
