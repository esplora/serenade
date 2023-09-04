<?php

namespace Esplora\Serenade\Tests;

use Esplora\Serenade\Message;
use PHPUnit\Framework\TestCase;

class MessageTest extends TestCase
{
    public function testToStringWithData()
    {
        $message = new Message('Hello, world!');

        $expected = "data: Hello, world!\n\n";
        $this->assertEquals($expected, (string) $message);
    }

    public function testToStringReplaceData()
    {
        $message = (new Message('Hello, world!'))->data('Some additional data');

        $expected = "data: Some additional data\n\n";
        $this->assertEquals($expected, (string) $message);
    }

    public function testToStringWithEvent()
    {
        $message = (new Message('Hello, world!'))->event('custom-event');

        $this->assertStringContainsString("data: Hello, world!\n\n", (string) $message);
        $this->assertStringContainsString("event: custom-event\n\n", (string) $message);
    }

    public function testToStringWithId(): void
    {
        $message = (new Message('Hello, world!'))->id('12345');

        $this->assertStringContainsString("id: 12345\n\n", (string) $message);
        $this->assertStringContainsString("data: Hello, world!\n\n", (string) $message);
    }

    public function testToStringWithRetry()
    {
        $message = (new Message('Hello, world!'))->retry(5000);

        $expected = "retry: 5000\n\ndata: Hello, world!\n\n";
        $this->assertEquals($expected, (string) $message);
    }

    public function testToStringWithAllProperties()
    {
        $message = (new Message('Hello, world!'))
            ->event('custom-event')
            ->id('12345')
            ->retry(5000);

        $expected = "retry: 5000\n\ndata: Hello, world!\n\n".
            "event: custom-event\n\nid: 12345\n\n";

        $this->assertSame($expected, (string) $message);
    }

    public function testMultipleData()
    {
        $message = new Message('');
        $result = $message->data("line 1\nline 2");

        $expected = "data: line 1\ndata: line 2\n\n";
        $this->assertEquals($expected, (string) $result);
    }
}
