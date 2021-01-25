<?php

namespace DanieleBarbaro\LaravelFullCalendar\Events;

interface IdentifiableEventInterface extends EventInterface
{

    /**
     * Get the event's ID
     *
     * @return int|string|null
     */
    public function getId();
}
