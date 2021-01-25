<?php

namespace DanieleBarbaro\LaravelFullCalendar\Events;

use Illuminate\Support\Collection;
use DanieleBarbaro\LaravelFullCalendar\Events\Event;

class EventCollection
{

    /**
     * @var Collection
     */
    protected $events;

    public function __construct()
    {
        $this->events = new Collection();
    }

    public function push(EventInterface $event, array $customAttributes = [])
    {
        $this->events->push($this->convertToArray($event, $customAttributes));
    }

    private function convertToArray(EventInterface $event, array $customAttributes = []): array
    {
        $eventArray = [
            'id' => $this->getEventId($event),
            'title' => $event->getTitle(),
            'allDay' => $event->isAllDay(),
            'start' => $event->getStart()->format('c'),
            'end' => $event->getEnd()->format('c'),
        ];

        $eventOptions = method_exists($event, 'getEventOptions') ? $event->getEventOptions() : [];

        return array_merge($eventArray, $eventOptions, $customAttributes);
    }

    private function getEventId(EventInterface $event)
    {
        if ($event instanceof IdentifiableEventInterface) {
            return $event->getId();
        }

        return null;
    }

    public function toJson(): string
    {
        return $this->events->toJson();
    }

    public function toArray(): array
    {
        return $this->events->toArray();
    }
}
