<?php

namespace Yetione\EventBus\Console;

use Yetione\EventBus\Services\EventBusService;
use Illuminate\Console\Command;

class Install extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'eventbus:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command for installing Event Bus';

    /**
     * Execute the console command.
     *
     * @param EventBusService $eventBusService
     * @return int
     */
    public function handle(EventBusService $eventBusService)
    {
        $eventBusService->createRootExchange();
        $this->info('Success!');
        return 0;
    }
}
