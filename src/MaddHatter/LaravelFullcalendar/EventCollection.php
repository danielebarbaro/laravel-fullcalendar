<?php namespace MaddHatter\LaravelFullcalendar;

class EventCollection
{
    protected $events;

    public function __construct()
    {
        $this->events = collect();
    }

    public function push(CalendarEvent $event, array $customAttributes = [])
    {
        $this->events->push($this->convertToArray($event, $customAttributes));
    }

    public function toJson(): string
    {
        return $this->events->toJson();
    }

    public function toArray(): array
    {
        return $this->events->toArray();
    }

    private function convertToArray(CalendarEvent $event, array $customAttributes = []): array
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

    private function getEventId(CalendarEvent $event)
    {
        if ($event instanceof IdentifiableEvent) {
            return $event->getId();
        }

        return null;
    }
}
