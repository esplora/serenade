<?php

namespace Esplora\Serenade;

use Illuminate\Broadcasting\Broadcasters\RedisBroadcaster;
use Illuminate\Http\Response;

class SerenadeBroadcaster extends RedisBroadcaster
{
    use UseSerenadeChannel;

    /**
     * The listener for Server-Sent Events
     *
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     *
     * This method returns a Symfony component for a response that streams
     * Server-Sent Events to the client.
     */
    public function listener(string $channel)
    {
        /** @var \Illuminate\Redis\Connections\PhpRedisConnection|\Illuminate\Redis\Connections\PredisConnection $connection */
        $connection = $this->redis->connection($this->connection);

        register_shutdown_function(function () use ($connection) {
            $connection->disconnect();

            abort(200);
        });

        return response()->stream(function () use ($connection, $channel) {

            // Send the message with a retry timer
            $this->message()->retry($this->timeRetry())->send();

            // If the Last-Event-Id header is set, restore the connection with the last message id.
            if (request()->hasHeader('Last-Event-Id')) {
                $this->restore(request()->header('Last-Event-Id'));
            }

            // Listen for new messages
            $connection->subscribe([$channel], fn (string $message) => $this->message($message));

        }, Response::HTTP_OK, $this->headers());
    }
}
