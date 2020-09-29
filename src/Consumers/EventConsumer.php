<?php


namespace Yetione\EventBus\Consumers;


use Yetione\EventBus\Events\EventFactory;
use Yetione\EventBus\Exceptions\EventInvalidException;
use Yetione\EventBus\Exceptions\ListenerException;
use Yetione\EventBus\Listeners\ListenerContact;
use PhpAmqpLib\Message\AMQPMessage;
use Yetione\RabbitMQ\Consumer\BasicConsumeConsumer;
use Yetione\RabbitMQ\DTO\Queue;

class EventConsumer extends BasicConsumeConsumer
{
    protected ListenerContact $listener;

    protected EventFactory $eventFactory;

    /**
     * @param AMQPMessage $message
     * @return int
     * @throws ListenerException
     * @throws EventInvalidException
     */
    protected function processMessage(AMQPMessage $message): int
    {
        if (!isset($this->listener)) {
            throw new ListenerException('Listener is not set');
        }
        $this->listener->setEvent($this->eventFactory->makeFromMessage($message));
        $result = $this->listener->handle($message);
        $this->listener->reset();
        return $result;
    }

    protected function createQueue(): Queue
    {
        return new Queue();
    }

    public function setListener(ListenerContact $listener): EventConsumer
    {
        $this->listener = $listener;
        return $this;
    }

    public function setEventFactory(EventFactory $eventFactory): EventConsumer
    {
        $this->eventFactory = $eventFactory;
        return $this;
    }


}
