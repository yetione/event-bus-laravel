<?php


namespace Yetione\EventBus\Services;


use Yetione\EventBus\Producers\SingleEventProducer;
use Yetione\RabbitMQ\DTO\Exchange;
use Yetione\RabbitMQ\DTO\ExchangeBinding;
use Yetione\RabbitMQ\DTO\Queue;
use Yetione\RabbitMQ\DTO\QueueBinding;
use Yetione\RabbitMQ\Producer\ProducerInterface;

class EventBusService
{
    protected ProducerInterface $producer;

    public function __construct(SingleEventProducer $producer)
    {
        $this->producer = $producer;
    }

    public function createRootExchange(): bool
    {
        $connection = $this->producer->getConnectionWrapper();
        return $connection->declareExchange($this->producer->getExchange(), true);
    }

    public function deleteRootExchange(): bool
    {
        $connection = $this->producer->getConnectionWrapper();
        return $connection->deleteExchange($this->producer->getExchange());
    }

    public function createEventSource(string $name): bool
    {
        list($routingKey, $queue, $exchange, $exchangeBinding, $queueBinding) = $this->getEventSourceData($name);
        $connection = $this->producer->getConnectionWrapper();

        return $connection->declareExchange($exchange, true) &&
            $connection->declareQueue($queue, true) &&
            $connection->declareExchangeBinding($exchangeBinding, true) &&
            $connection->declareQueueBinding($queueBinding);
    }

    public function deleteEventSource(string $name): bool
    {
        list($routingKey, $queue, $exchange, $exchangeBinding, $queueBinding) = $this->getEventSourceData($name);
        $connection = $this->producer->getConnectionWrapper();

        return $connection->unbindExchange($exchangeBinding) &&
            $connection->unbindQueue($queueBinding) &&
            $connection->deleteQueue($queue) &&
            $connection->deleteExchange($exchange);
    }

    public function createEventScope(string $name, string $source): bool
    {
        list($routingKey, $queue, $sourceExchange, $scopeExchange, $exchangeBinding, $queueBinding) = $this->getEventScopeData($name, $source);

        $connection = $this->producer->getConnectionWrapper();
        return $connection->declareExchange($scopeExchange, true) &&
            $connection->declareQueue($queue, true) &&
            $connection->declareExchangeBinding($exchangeBinding, true) &&
            $connection->declareQueueBinding($queueBinding, true);
    }

    public function deleteEventScope(string $name, string $source): bool
    {
        list($routingKey, $queue, $sourceExchange, $scopeExchange, $exchangeBinding, $queueBinding) = $this->getEventScopeData($name, $source);
        $connection = $this->producer->getConnectionWrapper();
        return $connection->unbindExchange($exchangeBinding) &&
            $connection->unbindQueue($queueBinding) &&
            $connection->deleteQueue($queue) &&
            $connection->deleteExchange($scopeExchange);
    }

    public function getEventSourceData(string $name): array
    {
        $exchange = $this->getEventSourceExchange($name);
        $queue = $this->getEventSourceQueue($name);
        $routingKey = $name.'.#';
        $exchangeBinding = (new ExchangeBinding($exchange, $this->producer->getExchange()))->setRoutingKey($routingKey);
        $queueBinding = (new QueueBinding($queue, $exchange))->setRoutingKey($routingKey);
        return [$routingKey, $queue, $exchange, $exchangeBinding, $queueBinding];
    }

    public function getEventSourceExchange(string $name): Exchange
    {
        return (new Exchange('events_sources.'.$name, \Yetione\RabbitMQ\Constant\Exchange::TYPE_TOPIC))
            ->setAutoDelete(false)
            ->setDurable(true)
            ->setTemporary(false)
            ->setPassive(false);
    }

    public function getEventSourceQueue(string $name): Queue
    {
        return (new Queue())
            ->setName('event_sources.'.$name.':queue')
            ->setPassive(false)
            ->setDurable(true)
            ->setAutoDelete(false);
    }

    public function getEventScopeData(string $name, string $source): array
    {
        $sourceExchange = $this->getEventSourceExchange($source);
        $scopeExchange = $this->getEventScopeExchange($name, $source);
        $routingKey = $source.'.'.$name;
        $binding = new ExchangeBinding($scopeExchange, $sourceExchange);
        $binding->setRoutingKey($routingKey);

        $queue = $this->getEventScopeQueue($name, $source);
        $queueBinding = (new QueueBinding($queue, $sourceExchange))->setRoutingKey($routingKey);
        return [$routingKey, $queue, $sourceExchange, $scopeExchange, $binding, $queueBinding];
    }

    public function getEventScopeExchange(string $name, string $source): Exchange
    {
        return (new Exchange('events_scopes.'.$source.'.'.$name, \Yetione\RabbitMQ\Constant\Exchange::TYPE_HEADERS))
            ->setAutoDelete(false)
            ->setDurable(true)
            ->setTemporary(false)
            ->setPassive(false);
    }

    public function getEventScopeQueue(string $name, string $source): Queue
    {
        return (new Queue())
            ->setName('events_scopes.'.$source.'.'.$name.':queue')
            ->setPassive(false)
            ->setDurable(true)
            ->setAutoDelete(false);
    }
}
