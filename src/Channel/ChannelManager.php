<?php

namespace Esplora\Serenade\Channel;

use Illuminate\Support\Collection;

class ChannelManager
{
    protected Collection $channel;

    /**
     *
     */
    public function __construct()
    {
        $this->channels = Collection::make();
    }

    /**
     * @param string $channelName
     *
     * @return \Esplora\Serenade\Channel\Channel
     */
    public function channel(string $channelName): BufferedChannel
    {
        if (!$this->channels->has($channelName)) {
            $this->channels->put($channelName, new BufferedChannel());
        }

        return $this->channels->get($channelName);
    }

    /**
     * @return void
     */
    public function clear()
    {
        $this->channels->each(function (BufferedChannel $channel) {
            $channel->clear();
        });

        $this->channels = Collection::make();
    }

    /**
     * @return array
     */
    public function all()
    {
        return $this->channels;
    }
}
