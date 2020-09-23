<?php


namespace Yetione\EventBus;


use Yetione\EventBus\Contracts\EventContract;
use Yetione\EventBus\Producers\SingleEventProducer;
use Illuminate\Support\Facades\Log;
use Yetione\RabbitMQ\Event\OnAfterPublishingMessageEvent;
use Yetione\RabbitMQ\Producer\ProducerInterface;

/**
 * TODO: механизм сохранения евентов для сервиса при его отключении
 *
 * Class EventBus
 * @package Yetione\EventBus
 */
class EventBus
{
    protected ProducerInterface $producer;

    public function __construct(SingleEventProducer $producer)
    {
        $this->producer = $producer;
        $this->producer->getEventDispatcher()->listen(OnAfterPublishingMessageEvent::class, function (OnAfterPublishingMessageEvent $event) {
            Log::debug('Event published.', [
                'body'=>$event->getMessage()->getBody(),
            ]);
        });
    }

    public function dispatch(EventContract $event)
    {
        $message = $this->producer->getMessageFactory()->fromArray(['event_payload'=>$event->payload()], $event->params(), ['event_name'=>$event->name()]);
        $routingKey = $this->buildRoutingKey($event);
        $this->producer->publish($message, $routingKey);
    }

    public function buildRoutingKey(EventContract $event): string
    {
        return "{$event->source()}.{$event->scope()}";
    }

    public function getProducer(): ProducerInterface
    {
        return $this->producer;
    }



}
