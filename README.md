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

## Keeping the socket server running with supervisord

The `serenade:serve` daemon needs to always be running in order to accept connections. This is a prime use case for `supervisor`, a task runner on Linux.

First, make sure `supervisor` is installed.

    # On Debian / Ubuntu
    apt install supervisor

    # On Red Hat / CentOS
    yum install supervisor
    systemctl enable supervisord

Once installed, add a new process that `supervisor` needs to keep running. You place your configurations in the `/etc/supervisor/conf.d` (Debian/Ubuntu) or `/etc/supervisord.d` (Red Hat/CentOS) directory.

Within that directory, create a new file called `sse.conf`.

    [program:sse]
    command=/usr/bin/php /home/laravel/artisan serenade:serve
    numprocs=1
    autostart=true
    autorestart=true
    user=laravel-user

Once created, instruct `supervisor` to reload its configuration files (without impacting the already running `supervisor` jobs).

    supervisorctl update
    supervisorctl start sse

Your echo server should now be running (you can verify this with `supervisorctl status`). If it were to crash, `supervisor` will automatically restart it.

Please note that, by default, `supervisor` will force a maximum number of open files onto all the processes that it manages. This is configured by the `minfds` parameter in `supervisord.conf`.

If you want to increase the maximum number of open files, you may do so in `/etc/supervisor/supervisord.conf` (Debian/Ubuntu) or `/etc/supervisord.conf` (Red Hat/CentOS):

    [supervisord]
    minfds=10240; (min. avail startup file descriptors;default 1024)

After changing this setting, you'll need to restart the supervisor process (which in turn will restart all your processes that it manages).


## SSL Support

Since most of the web's traffic is going through HTTPS, it's also crucial to secure your SSE server. Luckily, adding SSL support to this package is really simple.

### Using a reverse proxy

The easiest way to add SSL support is to use a reverse proxy like Nginx or Apache. This is the recommended way to add SSL support to your SSE server.

```bash
TODO:
```


## License

Serenade is open-sourced software licensed under the MIT license.
