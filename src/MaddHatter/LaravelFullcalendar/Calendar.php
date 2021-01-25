<?php namespace MaddHatter\LaravelFullcalendar;

use ArrayAccess;
use DateTime;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;
use Illuminate\View\Factory;

class Calendar
{

    protected $view;

    /**
     * @var EventCollection
     */
    protected $eventCollection;

    /**
     * @var string
     */
    protected $id;

    /**
     * Default options array
     *
     * @var array
     */
    protected $defaultOptions = [
        'header' => [
            'left' => 'prev,next today',
            'center' => 'title',
            'right' => 'month,agendaWeek,agendaDay',
        ],
        'eventLimit' => true,
    ];

    /**
     * User defined options
     *
     * @var array
     */
    protected $userOptions = [];

    /**
     * User defined callback options
     *
     * @var array
     */
    protected $callbacks = [];

    /**
     * @param Factory         $view
     * @param EventCollection $eventCollection
     */
    public function __construct(Factory $view, EventCollection $eventCollection)
    {
        $this->view            = $view;
        $this->eventCollection = $eventCollection;
    }

    /**
     * Create an event DTO to add to a calendar
     *
     * @param  string  $title
     * @param  bool  $isAllDay
     * @param  string|DateTime  $start  If string, must be valid datetime format: http://bit.ly/1z7QWbg
     * @param  string|DateTime  $end  If string, must be valid datetime format: http://bit.ly/1z7QWbg
     * @param  null  $id  event Id
     * @param  array  $options
     * @return SimpleEvent
     * @throws \Exception
     */
    public static function event(string $title, bool $isAllDay, $start, $end, $id = null, $options = []): SimpleEvent
    {
        return new SimpleEvent($title, $isAllDay, $start, $end, $id, $options);
    }

    /**
     * Create the <div> the calendar will be rendered into
     *
     * @return string
     */
    public function calendar(): string
    {
        return '<div id="calendar-' . $this->getId() . '"></div>';
    }

    /**
     * Get the <script> block to render the calendar (as a View)
     *
     * @return View|\Illuminate\View\View
     */
    public function script()
    {
        $options = $this->getOptionsJson();

        return $this->view->make('fullcalendar::script', [
            'id' => $this->getId(),
            'options' => $options,
        ]);
    }

    /**
     * Customize the ID of the generated <div>
     *
     * @param  string  $id
     * @return $this
     */
    public function setId(string $id): Calendar
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get the ID of the generated <div>
     * This value is randomized unless a custom value was set via setId
     *
     * @return string
     */
    public function getId(): string
    {
        if (! empty($this->id)) {
            return $this->id;
        }

        $this->id = Str::random(8);

        return $this->id;
    }

    /**
     * Add an event
     *
     * @param CalendarEvent $event
     * @param array $customAttributes
     * @return $this
     */
    public function addEvent(CalendarEvent $event, array $customAttributes = []): Calendar
    {
        $this->eventCollection->push($event, $customAttributes);

        return $this;
    }

    /**
     * Add multiple events
     *
     * @param array|ArrayAccess $events
     * @param array $customAttributes
     * @return $this
     */
    public function addEvents($events, array $customAttributes = []): Calendar
    {
        foreach ($events as $event) {
            $this->eventCollection->push($event, $customAttributes);
        }

        return $this;
    }

    /**
     * Set fullcalendar options
     *
     * @param array $options
     * @return $this
     */
    public function setOptions(array $options): Calendar
    {
        $this->userOptions = $options;

        return $this;
    }

    /**
     * Get the fullcalendar options (not including the events list)
     *
     * @return array
     */
    public function getOptions(): array
    {
        return array_merge($this->defaultOptions, $this->userOptions);
    }

    /**
     * Set fullcalendar callback options
     *
     * @param array $callbacks
     * @return $this
     */
    public function setCallbacks(array $callbacks): Calendar
    {
        $this->callbacks = $callbacks;

        return $this;
    }

    /**
     * Get the callbacks currently defined
     *
     * @return array
     */
    public function getCallbacks(): array
    {
        return $this->callbacks;
    }

    /**
     * Get options+events JSON
     *
     * @return string
     */
    public function getOptionsJson(): string
    {
        $options      = $this->getOptions();
        $placeholders = $this->getCallbackPlaceholders();
        $parameters   = array_merge($options, $placeholders);

        // Allow the user to override the events list with a url
        if (!isset($parameters['events'])) {
            $parameters['events'] = $this->eventCollection->toArray();
        }

        $json = json_encode($parameters);

        if ($placeholders) {
            return $this->replaceCallbackPlaceholders($json, $placeholders);
        }

        return $json;
    }

    /**
     * Generate placeholders for callbacks, will be replaced after JSON encoding
     *
     * @return array
     */
    protected function getCallbackPlaceholders(): array
    {
        $callbacks    = $this->getCallbacks();
        $placeholders = [];

        foreach ($callbacks as $name => $callback) {
            $placeholders[$name] = '[' . md5($callback) . ']';
        }

        return $placeholders;
    }

    /**
     * Replace placeholders with non-JSON encoded values
     *
     * @param $json
     * @param $placeholders
     * @return string
     */
    protected function replaceCallbackPlaceholders($json, $placeholders): string
    {
        $search  = [];
        $replace = [];

        foreach ($placeholders as $name => $placeholder) {
            $search[]  = '"' . $placeholder . '"';
            $replace[] = $this->getCallbacks()[$name];
        }

        return str_replace($search, $replace, $json);
    }
}
