<?php

namespace Esplora\Serenade;

use Illuminate\Broadcasting\Broadcasters\RedisBroadcaster;

class SerenadeBroadcaster extends RedisBroadcaster
{
    /**
     * Restores connection with last id
     *
     * @param string $id
     *
     * This method restores the connection with the last message id for the given Redis channel.
     */
    public function restore(string $id)
    {
        // TODO: ...
    }

    /**
     * The delay before reconnecting in milliseconds
     *
     * @return int
     *
     * This method returns an integer that represents the time in milliseconds
     * to wait before attempting to reconnect after a failure.
     */
    protected function timeRetry(): int
    {
        return 300;
    }

    /**
     * Sets headers for SSE response
     *
     * @return string[]
     *
     * This method returns an array of name-value pairs representing the headers
     * that should be sent in the Server-Sent Events (SSE) response.
     */
    protected function headers(): array
    {
        return [
            'Cache-Control'               => 'no-cache',
            'Content-Type'                => 'text/event-stream',
            'Connection'                  => 'keep-alive',
            'X-Accel-Buffering'           => 'no',
            'Access-Control-Allow-Origin' => '*',
        ];
    }

    /**
     * Returns a new SSE message
     *
     * @param mixed|null $data
     *
     * @return \Esplora\Serenade\Message
     *
     * This method returns a new message containing the given data, or an empty message if the data is null.
     */
    public function message(mixed $data = null)
    {
        $message = new Message((string) $data);

        return $message;
    }

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
