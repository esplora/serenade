<?php

namespace Esplora\Serenade\Channel;

use Esplora\Serenade\Encoder;
use Esplora\Serenade\Message;
use Illuminate\Support\Collection;
use React\Stream\WritableStreamInterface;

class BufferedChannel
{
    private Collection $streams;

    /**
     * Create a new BufferedChannel instance.
     */
    public function __construct()
    {
        $this->streams = Collection::make();
    }

    /**
     * Connect a stream to the channel.
     *
     * @param \React\Stream\WritableStreamInterface $stream
     * @param mixed|null                            $lastId
     *
     * @return void
     */
    public function connect(WritableStreamInterface $stream, $lastId = null): void
    {
        /* TODO: log storage mechanism
        if ($lastId !== null) {
            for ($i = $lastId; isset($this->bufferedData[$i]); ++$i) {
                $stream->write($this->bufferedData[$i]);
            }
        }*/

        $this->streams->push($stream);

        // TODO: The delay before reconnecting in milliseconds
        //$message = new Message();
        //$message->retry(300);
        //$stream->write();

        // TODO: Restores connection with last id
    }

    /**
     * Disconnect a stream from the channel.
     *
     * @param \React\Stream\WritableStreamInterface $stream
     *
     * @return void
     */
    public function disconnect(WritableStreamInterface $stream): void
    {
        $this->streams = $this->streams->filter(fn($connect) => $stream !== $connect);
    }

    /**
     * Write a message to all connected streams.
     *
     * @param \Esplora\Serenade\Message $message
     *
     * @return void
     */
    public function writeMessage(Message $message): void
    {
        foreach ($this->streams as $stream) {
            $stream->write($message);
        }
    }

    /**
     * Restore the connection with the last message ID for the given channel.
     *
     * @param string $id
     *
     * @return void
     */
    public function restore(string $id)
    {
        // TODO: ...
    }

    /**
     * Clear the channel.
     *
     * @return void
     */
    public function clear(): void
    {
        // TODO: ...
    }

    /**
     * Check if the channel is empty.
     *
     * @return bool
     */
    public function isEmpty(): bool
    {
        return $this->streams->isEmpty();
    }
}
