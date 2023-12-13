<?php

namespace Esplora\Serenade;

use Illuminate\Broadcasting\BroadcastController;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class ServerSentEventsServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        Broadcast::extend('serenade', function () {
            return new SerenadeBroadcaster();
        });

        Route::domain(config('broadcasting.connections.serenade.domain', ''))
            ->prefix(config('broadcasting.connections.serenade.prefix', ''))
            ->name(config('broadcasting.connections.serenade.route', 'serenade'))
            ->middleware(config('broadcasting.connections.serenade.middleware', []))
            ->get('/.well-known/serenade/{channel_name}', [BroadcastController::class, 'authenticate']);
    }
}
