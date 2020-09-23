<?php

namespace Yetione\EventBus\Console;

use Yetione\EventBus\Services\EventBusService;
use Illuminate\Console\Command;

class EventScope extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'eventbus:scopes
                            {action : Action for event scopes}
                            {--source= : Source name}
                            {name? : Optional scope name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command for manipulating with event scopes';

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
        $source = $this->option('source');
        switch ($action) {
            case 'create':
                if ($name && $source) {
                    $eventBusService->createEventScope($name, $source);
                } else {
                    $this->error('Name and source required');
                }
                break;
            case 'delete':
                if ($name && $source) {
                    $eventBusService->deleteEventScope($name, $source);
                } else {
                    $this->error('Name and source required');
                }
                break;
        }
        return 0;
    }
}
