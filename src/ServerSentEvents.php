<?php

namespace Esplora\Serenade;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Redis;

abstract class ServerSentEvents
{
    use UseSerenadeChannel;

    /**
     * Redis connection
     *
     * @return null
     */
    protected function connection()
    {
        return null;
    }

    /**
     * Redis channel
     *
     * @return string
     *
     * This method returns a string that represents the Redis channel,
     * which listens to the events.
     */
    protected function channel(): string
    {
        return 'serenade';
    }

    /**
     * Handles the incoming messages
     *
     * @param string $message
     *
     * This method handles the incoming messages for the subscribed channel.
     * It expects a string as input.
     */
    abstract public function handler(string $message);

    /**
     * The listener for Server-Sent Events
     *
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     *
     * This method returns a Symfony component for a response that streams
     * Server-Sent Events to the client.
     */
    public function listener()
    {
        /** @var \Illuminate\Redis\Connections\PhpRedisConnection|\Illuminate\Redis\Connections\PredisConnection $connection */
        $connection = Redis::connection($this->connection());

        register_shutdown_function(function () use ($connection) {
            $connection->disconnect();
            abort(200);
        });

        return response()->stream(function () use ($connection) {

            // Send the message with a retry timer
            $this->message()->retry($this->timeRetry())->send();

            // If the Last-Event-Id header is set, restore the connection with the last message id.
            if (request()->hasHeader('Last-Event-Id')) {
                $this->restore(request()->header('Last-Event-Id'));
            }

            // Listen for new messages
            $connection->subscribe([$this->channel()], fn (string $message) => $this->handler($message));

        }, Response::HTTP_OK, $this->headers());
    }
}
