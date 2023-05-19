<?php

namespace Esplora\Serenade;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Http\Request;
use Illuminate\Contracts\Broadcasting\Broadcaster;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class ServerSentEventsServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        Route::get('/serenade/{channel_name}', function (Request $request, Broadcaster $broadcaster) {
            /** @var \App\SerenadeBroadcaster $broadcaster */
            $broadcaster = $broadcaster;

            $access = (bool) $broadcaster->auth($request);

            abort_unless($access, AccessDeniedHttpException::class);

            return $broadcaster->listener();
        });

        /*
        Broadcast::extend('serenade', function ($broadcasting, $config) {
            return '';
        });
        */
    }
}
