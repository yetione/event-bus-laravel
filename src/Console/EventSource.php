<?php

namespace Yetione\EventBus\Console;

use Yetione\EventBus\Services\EventBusService;
use Illuminate\Console\Command;

class EventSource extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'eventbus:sources
                            {action : Action for event sources}
                            {name? : Optional source name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command for manipulating with event sources';

    /**
     * Execute the console command.
     *
     * @param EventBusService $eventBusService
     * @return int
     */
    public function handle(EventBusService $eventBusService)
    {
        $action = $this->argument('action');
        $name = $this->argument('name');
        switch ($action) {
            case 'create':
                if ($name) {
                    $eventBusService->createEventSource($name);
                } else {
                    $this->error('Name required');
                }
                break;
            case 'delete':
                if ($name) {
                    $eventBusService->deleteEventSource($name);
                } else {
                    $this->error('Name required');
                }
                break;
        }
        return 0;
    }
}
