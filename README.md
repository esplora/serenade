# Serenade

Serenade is a Laravel package for easily adding [Server-Sent Events](https://developer.mozilla.org/en-US/docs/Web/API/Server-sent_events) to your application. With Serenade, you can quickly and easily stream updates and events in real-time to your users, without the need for additional libraries or external dependencies.

## Features

* Simple and easy API for creating and streaming events.
* Compatible with any modern browser that supports Server-Sent Events.
* Full support for Laravel's built-in queuing system for efficient event broadcasting.
* Easily integrate events with Laravel's authentication system.
* Simple configuration and setup for easy integration into your existing Laravel application.

## Installation

To install Serenade, simply use Composer:

```bash
composer require esplora/serenade
```

## Usage

Using Serenade is simple and intuitive. Here's an example of how to send a simple event:

```php
//...
```

## Configure

All of your application's event broadcasting configuration is stored in the `config/broadcasting.php` configuration file.
Add the following lines accordingly:

```php
/*
|--------------------------------------------------------------------------
| Broadcast Connections
|--------------------------------------------------------------------------
|
| Here you may define all of the broadcast connections that will be used
| to broadcast events to other systems or over websockets. Samples of
| each available type of connection are provided inside this array.
|
*/

'connections' => [
    'serenade' => [
        'domain'     => '',
        'prefix'     => '',
        'route'      => 'serenade',
        'middleware' => 'web',
    ],
],
```



## Subscribing

Subscribing to updates from a web browser or any other platform supporting Server-Sent Events is straightforward:

```javascript
const url = new URL('https://localhost/.well-known/serenade/your.channel');

const eventSource = new EventSource(url);

// The callback will be called every time an update is published
eventSource.onmessage = e => console.log(e); // do something with the payload
```


This will create a new event with the name example and the message Hello, World!. For more advanced usage, including authorization and queuing, see the official documentation.

## License

Serenade is open-sourced software licensed under the MIT license.
