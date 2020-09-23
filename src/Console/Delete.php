<?php

namespace Yetione\EventBus\Console;

use Yetione\EventBus\Services\EventBusService;
use Illuminate\Console\Command;

class Delete extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'eventbus:delete';

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
        $eventBusService->deleteRootExchange();
        $this->info('Success!');
        return 0;
    }
}
