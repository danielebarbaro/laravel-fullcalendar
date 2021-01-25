<?php namespace MaddHatter\LaravelFullcalendar;

use DateTime;

interface CalendarEvent
{
    /**
     * Get the event's title
     *
     * @return string
     */
    public function getTitle(): string;

    /**
     * Is it an all day event?
     *
     * @return bool
     */
    public function isAllDay(): bool;

    /**
     * Get the start time
     *
     * @return DateTime
     */
    public function getStart(): DateTime;

    /**
     * Get the end time
     *
     * @return DateTime
     */
    public function getEnd(): DateTime;

}
