<?php


namespace Yetione\EventBus\Consumers;

use Yetione\EventBus\Exceptions\MakeEventConsumerFailed;
use Yetione\EventBus\Listeners\ListenerContact;
use Yetione\EventBus\Services\EventBusService;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Container\Container;
use Yetione\RabbitMQ\Consumer\ConsumerFactory as RabbitMQConsumerFactory;
use Yetione\RabbitMQ\DTO\Queue;
use Yetione\RabbitMQ\DTO\QueueBinding;

class ConsumerFactory
{
    protected RabbitMQConsumerFactory $rabbitMQFactory;

    protected EventBusService $eventBusService;

    protected Container $container;

    protected string $consumerName;

    public function __construct(
        RabbitMQConsumerFactory $rabbitMQFactory,
        EventBusService $eventBusService,
        Container $container
    )
    {
        $this->consumerName = config('event-bus.listener');
        $this->rabbitMQFactory = $rabbitMQFactory;
        $this->eventBusService = $eventBusService;
        $this->container = $container;
    }


    public function make(string $name): EventConsumer
    {
        if (empty($listenerOptions = config('event-bus.listeners.'.$name))) {
            throw new MakeEventConsumerFailed(sprintf('Creation of event listener [%s] failed', $name));
        }
        if (!isset($listenerOptions['listener'])) {
            throw new MakeEventConsumerFailed('Event listener is required');
        }
        if (!class_exists($listenerOptions['listener']) ||
            !is_subclass_of($listenerOptions['listener'], ListenerContact::class)) {
            throw new MakeEventConsumerFailed(sprintf(
                'Listener class [%s] must exists and be subclass of %s',
                $listenerOptions['listener'],
                ListenerContact::class
            ));
        }
        if ($listenerOptions['scope'] && $listenerOptions['source']) {
            $exchange = $this->eventBusService->getEventScopeExchange($listenerOptions['scope'], $listenerOptions['source']);
        } elseif ($listenerOptions['source']) {
            $exchange = $this->eventBusService->getEventSourceExchange($listenerOptions['source']);
        } else {
            throw new MakeEventConsumerFailed('Invalid listener options');
        }

        $queue = (new Queue())->setDurable(false)->setAutoDelete(true)->setTemporary(true);

        $binding = new QueueBinding($queue, $exchange);
        if (isset($listenerOptions['name']) && !empty($listenerOptions['name'])) {
            $binding->setArguments(['event_name'=>$listenerOptions['name']]);
        }
        try {
            /** @var ListenerContact $listener */
            $listener = $this->container->make($listenerOptions['listener']);
        } catch (BindingResolutionException $e) {
            throw new MakeEventConsumerFailed($e->getMessage(), $e->getCode(), $e);
        }
        /** @var EventConsumer $consumer */
        $consumer = $this->rabbitMQFactory->make($this->consumerName, $name);
        $connection = $consumer->getConnectionWrapper();
        $connection->declareQueue($queue, true);
        $connection->declareQueueBinding($binding);
        $consumer->setQueue($queue);
        $consumer->setListener($listener);
        return $consumer;

    }
}
