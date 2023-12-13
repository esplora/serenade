<?php

namespace Esplora\Serenade;

use Illuminate\Broadcasting\Broadcasters\Broadcaster;
use Illuminate\Broadcasting\Broadcasters\UsePusherChannelConventions;
use Illuminate\Broadcasting\BroadcastException;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class SerenadeBroadcaster extends Broadcaster
{
    use UsePusherChannelConventions;

    /**
     * Authenticate the incoming request for a given channel.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @throws \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException
     *
     * @return mixed
     */
    public function auth($request)
    {
        $channelName = $this->normalizeChannelName($request->channel_name);

        if (empty($request->channel_name) ||
            ($this->isGuardedChannel($request->channel_name) &&
                ! $this->retrieveUser($request, $channelName))) {
            throw new AccessDeniedHttpException;
        }

        return parent::verifyUserCanAccessChannel(
            $request, $channelName
        );
    }

    /**
     * Return the valid authentication response.
     *
     * @param \Illuminate\Http\Request $request
     * @param mixed                    $result
     *
     * @return mixed
     */
    public function validAuthenticationResponse($request, $result)
    {
        if (is_bool($result)) {
            return json_encode($result);
        }

        $channelName = $this->normalizeChannelName($request->channel_name);

        $user = $this->retrieveUser($request, $channelName);

        $broadcastIdentifier = method_exists($user, 'getAuthIdentifierForBroadcasting')
            ? $user->getAuthIdentifierForBroadcasting()
            : $user->getAuthIdentifier();

        return json_encode(['channel_data' => [
            'user_id'   => $broadcastIdentifier,
            'user_info' => $result,
        ]]);
    }

    /**
     * Broadcast the given event.
     *
     * @param array  $channels
     * @param string $event
     * @param array  $payload
     *
     * @throws \Illuminate\Broadcasting\BroadcastException
     *
     * @return void
     */
    public function broadcast(array $channels, $event, array $payload = [])
    {
        if (empty($channels)) {
            return;
        }

        try {
            foreach ($channels as $channel) {
                Http::post("http://127.0.0.1:8080/.well-known/serenade/{$channel->name}", [
                    'channel' => $channel->name,
                    'event'   => $event,
                    'data'    => $payload,
                ]);
            }
        } catch (\Throwable $e) {
            throw new BroadcastException(
                sprintf('Serenade error: %s.', $e->getMessage())
            );
        }
    }
}
