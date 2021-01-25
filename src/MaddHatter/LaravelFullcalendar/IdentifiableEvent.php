<?php namespace MaddHatter\LaravelFullcalendar;

interface IdentifiableEvent extends CalendarEvent
{

    /**
     * Get the event's ID
     *
     * @return int|string|null
     */
    public function getId();

}
