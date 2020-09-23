<?php


namespace Yetione\EventBus\Consumers;


use Yetione\EventBus\Exceptions\ListenerException;
use Yetione\EventBus\Listeners\ListenerContact;
use PhpAmqpLib\Message\AMQPMessage;
use Yetione\RabbitMQ\Consumer\BasicConsumeConsumer;
use Yetione\RabbitMQ\DTO\Queue;
use Yetione\RabbitMQ\Exception\StopConsumerException;

class EventConsumer extends BasicConsumeConsumer
{
    protected ListenerContact $listener;

    /**
     * @param AMQPMessage $message
     * @return int
     * @throws ListenerException
     */
    protected function processMessage(AMQPMessage $message): int
    {
        if (!isset($this->listener)) {
            throw new ListenerException('Listener is not set');
        }
        return $this->listener->handle($message);
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


}
