<?php


namespace Yetione\EventBus\Listeners;


use Yetione\EventBus\Exceptions\EventNameMissingException;
use Illuminate\Support\Facades\Log;
use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Wire\AMQPAbstractCollection;
use Yetione\Json\Json;
use Yetione\RabbitMQ\Configs\ExchangesConfig;
use Yetione\RabbitMQ\Constant\Consumer;

class TestListener extends AbstractListener
{
    protected ExchangesConfig $exchangesConfig;

    public function __construct(ExchangesConfig $exchangesConfig)
    {
        $this->exchangesConfig = $exchangesConfig;
    }

    public function handle(AMQPMessage $message): int
    {
        $eventName = $this->requireEventName($message);
        $body = Json::decode($message->body, true);
        Log::debug('Listener receive message.', [
            'event_name'=>$eventName,
            'body'=>$body, 'routing_key'=>$message->getRoutingKey(),
        ]);
        return Consumer::RESULT_SUCCESS;
    }


}
