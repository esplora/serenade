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
        'driver'     => 'serenade',
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


## Starting the SSE server

Once you have configured your Serenade apps, you can start the Laravel Serenade server by issuing the artisan command:

```bash
php artisan serenade:serve
```

### Using a different port

The default port of the Laravel Serenade server is `6001`. You may pass a different port to the command using the `--port` option.

```bash
php artisan serenade:serve --port=3030
```

This will start listening on port `3030`.

### Restricting the listening host

By default, the Laravel Serenade server will listen on `0.0.0.0` and will allow incoming connections from all networks. If you want to restrict this, you can start the server with a `--host` option, followed by an IP.

For example, by using `127.0.0.1`, you will only allow WebSocket connections from localhost.

```bash
php artisan serenade:serve --host=127.0.0.1
```

## SSL Support

Since most of the web's traffic is going through HTTPS, it's also crucial to secure your SSE server. Luckily, adding SSL support to this package is really simple.

### Using a reverse proxy

The easiest way to add SSL support is to use a reverse proxy like Nginx or Apache. This is the recommended way to add SSL support to your SSE server.

```bash
TODO:
```


## License

Serenade is open-sourced software licensed under the MIT license.
