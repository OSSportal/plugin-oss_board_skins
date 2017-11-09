<?php
namespace Xpressengine\Plugins\OSSBoardSkins\Components\Calendar;

use Illuminate\Support\Fluent;

class DateVO extends Fluent
{
    /**
     * @var array
     *
     * [
        'day' => 1, // 0 ~ 6 from sunday to saturday
        'date' => '2017-09-10',
    ];
     */
    protected $attributes = [];

    /**
     * @var array
     * [
    'year' => 2017,
    'month' => 9,
    'day' => 10,
    ]
     */
    protected $parts = [];

    /**
     * @var array
     * [
        [
            'title' => '물의 날',
            'type' => 'event',
        ],
        [
            'title' => '어린이 날',
            'type' => 'holiday',
        ],
    ]
     */
    protected $events = [];

    protected $eventTypes = [
        self::EVENT_TYPE_EVENT,
        self::EVENT_TYPE_HOLIDAY,
    ];

    const EVENT_TYPE_EVENT = 'event';
    const EVENT_TYPE_HOLIDAY = 'holiday';
    const EVENT_TYPE_CELEBRATE = 'celebrate';

    public function fill($time)
    {
        $date = date('Y-m-d', $time);
        $this->attributes = [
            'time' => $time,
            'day' => date('w', $time),
            'date' => $date,
        ];

        $parts = explode('-', $date);
        $this->parts = [
            'year' => $parts[0],
            'month' => $parts[1],
            'day' => $parts[2],
        ];
    }

    public function setEvent($type, $title, $link)
    {
        if (!in_array($type, $this->eventTypes)) {
            throw new EventTypeNotFoundException;
        }

        $this->events[] = [
            'type' => $type,
            'title' => $title,
            'link' => $link,
        ];
    }

    public function getEvents()
    {
        return $this->events;
    }

    public function getParts($part = 'day')
    {
        return $this->parts[$part];
    }
}
