<?php


namespace Yetione\EventBus\Jobs;


use Yetione\EventBus\Services\EventBusService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Yetione\RabbitMQ\DTO\Exchange;
use Yetione\RabbitMQ\DTO\ExchangeBinding;

class CreateEventScope implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    protected string $name;

    protected string $source;

    public function __construct(string $name, string $source)
    {
        $this->name = $name;
        $this->source = $source;
    }

    public function handle(EventBusService $eventBusService)
    {
        return $eventBusService->createEventScope($this->name, $this->source);
    }
}
