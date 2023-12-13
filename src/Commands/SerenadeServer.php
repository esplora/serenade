<?php

namespace Esplora\Serenade\Commands;

use Esplora\Serenade\Server\Server;
use Illuminate\Console\Command;

class SerenadeServer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'serenade:serve
        {--host=0.0.0.0}
        {--port=6001}
    ';

    /**
     * The console command description.
     *
     * @var string|null
     */
    protected $description = 'Start the Serenade server.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $server = new Server();

        $server->start();;
    }
}
