<?php


namespace Yetione\EventBus\Jobs;


use Yetione\EventBus\Services\EventBusService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;

class CreateEventSource implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    protected string $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function handle(EventBusService $eventBusService)
    {
        return $eventBusService->createEventSource($this->name);
    }
}
