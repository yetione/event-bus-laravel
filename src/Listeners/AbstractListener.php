<?php


namespace Yetione\EventBus\Listeners;


use Yetione\EventBus\Event;

abstract class AbstractListener implements ListenerContact
{
    protected Event $event;

    public function getEvent(): Event
    {
        return $this->event;
    }

    public function setEvent(Event $event): ListenerContact
    {
        $this->event = $event;
        return $this;
    }

    public function reset(): void
    {
        unset($this->event);
    }
}
