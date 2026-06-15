<?php

namespace App\Listeners;

use App\Events\CsvCreated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendCsvNotification
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {

    }

    /**
     * Handle the event.
     */
    //data returned
    public function handle(CsvCreated $event)
    {
        $url = $event->url;
        return $url;
    }
}
