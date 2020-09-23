<?php


namespace Yetione\EventBus\Providers;


use Yetione\EventBus\Console\Delete;
use Yetione\EventBus\Console\EventScope;
use Yetione\EventBus\Console\EventSource;
use Yetione\EventBus\Console\Install;
use Yetione\EventBus\Console\RunConsumer;
use Yetione\EventBus\Producers\SingleEventProducer;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use Yetione\RabbitMQ\Producer\ProducerFactory;
use Yetione\RabbitMQ\Producer\ProducerInterface;

class EventBusServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../../config/event-bus.php', 'event-bus');
        $this->app->singleton(SingleEventProducer::class, static function(Application $app): ProducerInterface {
            /** @var ProducerFactory $producerFactory */
            $producerFactory = $app->make(ProducerFactory::class);
            return $producerFactory->make(config('event-bus.producer'));
        });
    }

    public function boot()
    {
        $this->publishes([
            __DIR__.'/../../config/event-bus.php' => config_path('event-bus.php'),
        ], 'event-bus');
        if ($this->app->runningInConsole()) {
            $this->commands([
                Install::class,
                Delete::class,
                EventSource::class,
                EventScope::class,
                RunConsumer::class
            ]);
        }
    }
}
