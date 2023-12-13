<?php

namespace Esplora\Serenade\Server;

use Esplora\Serenade\Channel\BufferedChannel;
use Esplora\Serenade\Channel\ChannelManager;
use Esplora\Serenade\Message;
use Esplora\Serenade\UseSerenadeChannel;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Psr\Http\Message\ServerRequestInterface;
use React\EventLoop\LoopInterface;
use React\Http\HttpServer;
use React\Http\Message\Response;
use React\Socket\SocketServer;
use React\EventLoop\Loop;
use React\Stream\ThroughStream;
use Symfony\Bridge\PsrHttpMessage\Factory\HttpFoundationFactory;

class Server
{
    use UseSerenadeChannel;

    /**
     * @var \React\EventLoop\LoopInterface The event loop instance.
     */
    protected LoopInterface $loop;

    /**
     * @var \Esplora\Serenade\Channel\ChannelManager The channel manager instance.
     */
    protected ChannelManager $channelManager;

    /**
     * @var string The URI for the server.
     */
    private string $uri;

    /**
     * Create a new Server instance.
     *
     * @param string $uri The URI for the server (default: '127.0.0.1:8080').
     */
    public function __construct(string $uri = '127.0.0.1:8080')
    {
        $this->uri = $uri;
        $this->loop = Loop::get();
        $this->channelManager = new ChannelManager();
    }

    /**
     * Start the server.
     *
     * @return void
     */
    public function start()
    {
        $http = new HttpServer($this->loop, function (ServerRequestInterface $request) {

            $channelName = Str::of($request->getUri()->getPath())
                ->after('/serenade/')
                ->toString();

            $bufferedChannel = $this->channelManager->channel($channelName);

            return $request->getMethod() === 'GET'
                ? $this->handleSubscriptionRequest($request, $bufferedChannel)
                : $this->handlePublicationRequest($request, $bufferedChannel);
        });

        $socket = new SocketServer($this->uri);

        $http->listen($socket);

/*
        $this->loop->addPeriodicTimer(2.0, function () {

            $message = $this->message('ticking')
                ->event('tick')
                ->id(Str::orderedUuid()->toString());

            $this->channelManager->all()->each(fn(BufferedChannel $channel) => $channel->writeMessage($message));
        });
*/

        echo "Server running at {$this->uri}" . PHP_EOL;
    }

    /**
     * Handle Publication request.
     *
     * The publisher sends updates by issuing POST HTTPS requests on the hub URL.
     * When it receives an update, the hub dispatches it to subscribers using the established server-sent events connections.
     *
     * @param \Psr\Http\Message\ServerRequestInterface  $request
     * @param \Esplora\Serenade\Channel\BufferedChannel $channel
     *
     * @return \React\Http\Message\Response
     */
    public function handlePublicationRequest(ServerRequestInterface $request, BufferedChannel $channel)
    {
        $content = json_decode($request->getBody()->getContents(), true);

        $data = json_encode(Arr::get($content, 'data', []));

        $message = $this->message($data)
            ->event('OrderShipmentStatusUpdated')
            ->id(Str::orderedUuid()->toString());

        $channel
            ->writeMessage($message);

        return new Response(Response::STATUS_OK, [
            'Access-Control-Allow-Origin' => '*',
            'Content-Type'                => 'text/json',
        ]);
    }


    /**
     * Handle Subscription request.
     *
     * The subscriber subscribes to a URL exposed by a hub to receive updates from one or many topics.
     * To subscribe to updates, the client opens an HTTPS connection following the Server-Sent Events specification (W3C.REC-eventsource-20150203)
     * to the hub's subscription URL advertised by the publisher. The GET HTTP method must be used.
     * The connection SHOULD use HTTP version 2 or superior to leverage multiplexing and other performance-oriented related features provided by these versions.
     *
     * @param \Psr\Http\Message\ServerRequestInterface  $request
     * @param \Esplora\Serenade\Channel\BufferedChannel $channel
     *
     * @return \React\Http\Message\Response
     * @throws \Exception
     */
    public function handleSubscriptionRequest(ServerRequestInterface $request, BufferedChannel $channel)
    {
        if (!$this->isLaravelAuthorized($request)) {
            return new Response(Response::STATUS_UNAUTHORIZED, [
                'Access-Control-Allow-Origin' => '*',
                'Content-Type'                => 'text/html',
            ], 'Unauthorized');
        }

        $stream = new ThroughStream();

        $id = $request->getHeaderLine('Last-Event-ID');

        $this->loop->futureTick(fn() => $channel->connect($stream, $id));

        $stream->on('close', fn() => $channel->disconnect($stream));

        /*
        $stream->on('error', function (Exception $exception) {
            echo $exception->getMessage() . PHP_EOL;
        });
        */

        return new Response(Response::STATUS_OK, $this->headers(), $stream);
    }


    /**
     * Check if request is authorized in Laravel.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     *
     * @return bool
     * @throws \Exception
     */
    public function isLaravelAuthorized(ServerRequestInterface $request): bool
    {
        $httpFoundationFactory = new HttpFoundationFactory();
        $symfonyRequest = $httpFoundationFactory->createRequest($request);
        $laravelRequest = \Illuminate\Http\Request::createFromBase($symfonyRequest);

        $response = app()->handle($laravelRequest);

        return $response->getContent() === 'true';
    }
}
