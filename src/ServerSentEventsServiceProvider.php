<?php

namespace Serenade\Live;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Broadcast;

class ServerSentEventsServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        Broadcast::extend('serenade', function ($broadcasting, $config) {
            return '';
        });
    }
}
