<?php

namespace Esplora\Serenade;

trait UseSerenadeChannel
{
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
}
