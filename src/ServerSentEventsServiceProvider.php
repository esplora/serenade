<?php

namespace Esplora\Serenade;

use Illuminate\Contracts\Broadcasting\Broadcaster;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class ServerSentEventsServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        Route::domain(config('broadcasting.connections.serenade.domain', ''))
            ->prefix(config('broadcasting.connections.serenade.prefix', ''))
            ->name(config('broadcasting.connections.serenade.route', 'serenade'))
            ->middleware(config('broadcasting.connections.serenade.middleware', 'web'))
            ->get('/.well-known/serenade/{channel_name}', function (Request $request, Broadcaster $broadcaster) {
                /** @var \Esplora\Serenade\SerenadeBroadcaster $broadcaster */
                $broadcaster = $broadcaster;

                $access = (bool) $broadcaster->auth($request);

                abort_unless($access, AccessDeniedHttpException::class);

                return $broadcaster->listener();
            });

        Broadcast::extend('serenade', fn ($broadcasting, $config) => $this->createDriver($config));
    }

    /**
     * Create an instance of the driver.
     *
     * @param array $config
     *
     * @return \Illuminate\Contracts\Broadcasting\Broadcaster
     */
    protected function createDriver(array $config)
    {
        return new SerenadeBroadcaster(
            $this->app->make('redis'), $config['connection'] ?? null,
            $this->app['config']->get('database.redis.options.prefix', '')
        );
    }
}
