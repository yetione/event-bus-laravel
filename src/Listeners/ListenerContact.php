<?php


namespace Yetione\EventBus\Listeners;


use Yetione\EventBus\Event;
use Yetione\EventBus\Exceptions\ListenerException;
use PhpAmqpLib\Message\AMQPMessage;

interface ListenerContact
{
    /**
     * @param AMQPMessage $message
     * @return int
     * @throws ListenerException
     */
    public function handle(AMQPMessage $message): int;

    public function setEvent(Event $event): ListenerContact;

    public function getEvent(): Event;

    public function reset(): void;
}
