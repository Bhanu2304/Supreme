<?php

namespace App\Listeners;

use App\Events\HODispatched;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\SCInwardInventoryPart;


class SendDispatchedNotification
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
     * @param  HODispatched  $event
     * @return void
     */
    public function handle(HODispatched $event)
    {
        //$SCInwardInventoryPart = SCInwardInventoryPart::whereRaw("center_id='$center_id'")->first();
        $center_id = $event->center_id;
        DB::insert("insert into product_upload set brand='$center_id', category='2', product='3', model='4'");
    }
}
