<?php


namespace Yetione\EventBus\Listeners;


use Yetione\EventBus\Exceptions\EventNameMissingException;
use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Wire\AMQPAbstractCollection;

abstract class AbstractListener implements ListenerContact
{
    /**
     * @param AMQPMessage $message
     * @return string
     * @throws EventNameMissingException
     */
    protected function requireEventName(AMQPMessage $message): string
    {
        if (($headers = $message->get('application_headers')) && $headers instanceof AMQPAbstractCollection) {
            if (is_array($data = $headers->getNativeData()) && isset($data['event_name'])) {
                return (string) $data['event_name'];
            }
        }
        throw new EventNameMissingException('Event name is missing');
    }
}
