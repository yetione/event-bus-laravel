<?php


namespace Yetione\EventBus\Events;


use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Wire\AMQPAbstractCollection;
use Yetione\EventBus\Event;
use Yetione\EventBus\Exceptions\EventInvalidException;
use Yetione\Json\Exceptions\JsonException;
use Yetione\Json\Json;

class EventFactory
{

    public function __construct()
    {
    }

    /**
     * @param AMQPMessage $message
     * @return Event
     * @throws EventInvalidException
     */
    public function makeFromMessage(AMQPMessage $message): Event
    {
        $event = new Event();
        if (($headers = $message->get('application_headers')) && $headers instanceof AMQPAbstractCollection && is_array($data = $headers->getNativeData())) {
            if (!isset($data['event_name'])) {
                throw new EventInvalidException('Event name is missing');
            }
            $event->setName($data['event_name']);
            if (isset($data['x-event_source'])) {
                $event->setSource($data['x-event_source']);
            }
            if (isset($data['x-event_scope'])) {
                $event->setScope($data['x-event_scope']);
            }
        }
        if ('application/json' === $message->get('content_type')) {
            try {
                $decodedBody = Json::decode($message->body, true);
                if (!isset($decodedBody['event_payload'])) {
                    throw new EventInvalidException('Event payload is missing');
                }
                $event->setPayload($decodedBody['event_payload']);
            } catch (JsonException $e) {}
        }
        return $event;
    }
}