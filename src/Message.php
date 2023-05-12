<?php

namespace Serenade\Live;

use Stringable;

/**
 * The Message class formats a message according to the Server-Sent Events (SSE) specification.
 */
class Message
{
    /**
     * The body of the message. Several data values in a row are interpreted as one message separated by line breaks \n.
     *
     * @var string $data
     */
    protected $data;

    /**
     * The id property of the message. Used to update the Last-Event-ID sent in the header on reconnects.
     *
     * @var string $id
     */
    protected $id;

    /**
     * The recommended delay before reconnecting in milliseconds. Cannot be set using JavaScript.
     *
     * @var int $retry
     */
    protected $retry;

    /**
     * The user-defined event name. Should be set before the data field.
     *
     * @var string $event
     */
    protected $event;

    /**
     * Message constructor.
     *
     * @param string $data The body of the message.
     */
    public function __construct(string $data)
    {
        $this->data = $data;
    }

    /**
     * Sets the body of the message.
     *
     * @param string $data The body of the message.
     *
     * @return $this
     */
    public function data(string $data): static
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Sets the id property of the message.
     *
     * @param string $id The id property of the message.
     *
     * @return $this
     */
    public function id(string $id): static
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Sets the user-defined event name of the message.
     *
     * @param string $event The user-defined event name of the message.
     *
     * @return $this
     */
    public function event(string $event): static
    {
        $this->event = $event;

        return $this;
    }

    /**
     * Sets the retry property of the message.
     *
     * @param int $retry The recommended delay before reconnecting in milliseconds.
     *
     * @return $this
     */
    public function retry(int $retry): static
    {
        $this->retry = $retry;

        return $this;
    }

    /**
     * Returns the message in the form of a string.
     *
     * @return string The message in the form of a string.
     */
    public function __toString(): string
    {
        $message = '';

        if ($this->retry) {
            $message .= sprintf("retry: (%s)\n\n", $this->retry);
        }

        if ($this->data) {
            $message .= $this->multipleDate();
        }

        if ($this->event) {
            $message .= sprintf("event: (%s)\n\n", $this->event);
        }

        if ($this->id) {
            $message .= sprintf("id: (%s)\n\n", $this->id);
        }

        return $message;
    }

    /**
     * Sends the message and flushes the buffer.
     *
     * @param string|null $event The user-defined event name of the message.
     */
    public function send(string $event = null): void
    {
        if ($event !== null) {
            $this->event = $event;
        }

        echo $this;

        ob_flush();
        flush();
    }

    /**
     * Formats multiple data lines by prefixing each line with "data: ".
     *
     * @return string The formatted data string.
     */
    protected function multipleDate(): string
    {
        return implode("\n", array_map(fn($line) => 'data: ' . $line, explode("\n", $this->data))) . "\n\n";
    }
}
