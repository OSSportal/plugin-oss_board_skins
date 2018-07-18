<?php
namespace Xpressengine\Plugins\OSSBoardSkins\Components\Calendar;

use Xpressengine\Permission\Instance;
use Xpressengine\Plugins\Board\BoardPermissionHandler;
use Xpressengine\Plugins\OSSBoardSkins\Components\OSS\OSSSkin;
use Xpressengine\Plugins\Board\Models\Board;
use Gate;

class CalendarSkin extends OSSSkin
{
    protected static $path = 'oss_board_skins/components/Calendar';

    /**
     * render
     *
     * @return \Illuminate\Contracts\Support\Renderable|string
     */
    public function render()
    {
        /** @var \Xpressengine\Http\Request $request */
        $request = app('request');

        $listStyle = $request->get('listStyle', 'list');
        $this->data['listStyle'] = $listStyle;
        $this->data['_originSkinPath'] = static::$path;
        $this->data['_parentSkinPath'] = parent::$path;
        $this->data['withoutList'] = true;

        $boardPermission = app('Xpressengine\Plugins\Board\BoardPermissionHandler');
        $this->data['createPermission'] = Gate::allows(
            BoardPermissionHandler::ACTION_CREATE,
            new Instance($boardPermission->name($this->data['instanceId']))
        );
        if ($listStyle == 'calendar' && $this->view == 'index') {
            $this->calendar();
        } elseif ($this->view === 'show') {
            $this->intercept();
        }
        

        return parent::render();
    }

    protected function intercept()
    {
        intercept('XeEditor@compile', 'attach.host.to.img', function ($method, $instanceId, $content, $htmlable = false) {
            $content = $method($instanceId, $content, $htmlable);
            $content = preg_replace_callback('~<img.*?src=["\']+(.*?)["\']+~', function ($match) {
                return str_replace($match[1], asset($match[1]), $match[0]);
            }, $content);
            return $content;
        });
    }

    protected function calendar()
    {
        /** @var \Xpressengine\Http\Request $request */
        $request = app('request');

        $calendarMonth = $request->get('calendar_month');
        if ($calendarMonth == null) {
            $calendarStartYear = $request->get('calendarStartYear', date('Y'));
            $calendarStartMonth = $request->get('calendarStartMonth', date('m'));
        } else {
            $parts = explode('-', $calendarMonth);
            $calendarStartYear = $parts[0];
            $calendarStartMonth = $parts[1];
        }

        // use calendar api or cached information
        $calendar = [];

        // start week
        $standardTime = strtotime(sprintf('%s-%s-01', $calendarStartYear, $calendarStartMonth));

        if (date('w', $standardTime) == 0) {
            $startTime = strtotime('sunday', $standardTime);
        } else {
            $startTime = strtotime('last sunday', $standardTime);
        }

        $endTime = strtotime('+1 month', $standardTime) - 86400;
        if (date('w', $endTime) != 6) {
            $endTime = strtotime('saturday', $endTime);
        }


        // 기존 안
        $model = Board::division($this->data['instanceId']);
        $query = $model->where('instance_id', $this->data['instanceId'])->visible();
        $query->getProxyManager()->wheres($query->getQuery(), [
            'seminar_event_started_at' => [date('Y-m-d 00:00:00', $startTime), '>='],
            'seminar_event_ended_at' => [date('Y-m-d 23:59:59', $endTime), '<='],
        ]);
        $items = $query->orderBy('seminar_event_started_at', 'asc')
            ->orderBy('seminar_event_ended_at', 'desc')->get();

        $urlHandler = app('Xpressengine\Plugins\Board\UrlHandler');

        $events = [];
        foreach ($items as $item) {
            $startEventTime = strtotime($item['seminar_event_started_at']);
            $endEventTime = strtotime($item['seminar_event_ended_at']);
            for ($time = $startEventTime; $time <= $endEventTime; $time = $time + 86400) {
                $date = date('Y-m-d', $time);
                if (isset($events[$date]) == false) {
                    $events[$date] = [];
                }
                $events[$date][] = [
                    'type' => DateVO::EVENT_TYPE_EVENT,
                    'title' => $item['title'],
                    'link' => $urlHandler->getShow($item, $request->all()),
                ];
            }
        }

        $line = 0;
        for ($time = $startTime; $time <= $endTime; $time = $time + 86400) {
            $week = date('w', $time);
            if ($week == 0) {
                $calendar[$line] = [];
            }
            $dateVO = new DateVO();
            $dateVO->fill($time);

            if (isset($events[$dateVO['date']])) {
                foreach ($events[$dateVO['date']] as $event) {
                    $dateVO->setEvent($event['type'], $event['title'], $event['link']);
                }
            }

            $calendar[$line][] = $dateVO;

            if ($week == 6) {
                ++$line;
            }
        }

        // 두번째 안
        $calendarTime = [];
        $timeToLine = [];
        $dateDots = [];
        $line = 0;
        for ($time = $startTime; $time <= $endTime; $time = $time + 86400) {
            $week = date('w', $time);
            if ($week == 0) {
                $calendarTime[$line] = [];
            }

            $calendarTime[$line][$week] = $time;
            $timeToLine[$time] = [$line, $week];
            $dateDots[$line][$week] = ['', '', '', '', '', '', '', ''];
            $eventLines[$line][$week] = [];
            if ($week == 6) {
                ++$line;
            }
        }
        foreach ($items as $item) {
            $startEventTime = strtotime($item['seminar_event_started_at']);
            $endEventTime = strtotime($item['seminar_event_ended_at']);
            $gage = 0;
            $dot = 0;

            for ($time = $startEventTime; $time <= $endEventTime; $time = $time + 86400) {
                if (isset($timeToLine[$time]) == false) {
                    continue;
                }
                $lineInfo = $timeToLine[$time];
                $line = $lineInfo[0];
                $week = $lineInfo[1];

                for($doti = $dot; $limit = count($dateDots[$line][$week]); ++$doti) {
                    if ($dateDots[$line][$week][$doti] == '') {
//                        dump($dateDots[$line][$week][$doti] == '');
//                        echo ' before=' . $dateDots[$line][$week][$doti] . "<br>";
//                        echo '-----set dots' . $line . '-' . $week . '-' . $doti ."<br>";
                        $dateDots[$line][$week][$doti] = $item->id;
			            $dot = $doti;
                        break;
                    }
                }

                $gage = $gage + 1;
                if ($week == 6) {
                    $dotWeek = ($week+1)-$gage;
    //                echo 'set line ' . $line . '-' . $dotWeek .  '-' . $gage . " {$item->id} <br>";
                    $eventLines[$line][$dotWeek][] = [
                        'item' => $item,
                        'gage' => $gage,
                        'dot' => array_search($item->id, $dateDots[$line][$dotWeek]),
                    ];
                    $gage = 0;
                    $dot = 0;
                }
            }

            if ($gage > 0) {
                // 0 2
                $dotWeek = ($week+1)-$gage;

  //              echo 'set line out of loof ' . $line .  '-' . $dotWeek . '-' . $gage . " {$item->id} <br>";

                $eventLines[$line][$dotWeek][] = [
                    'item' => $item,
                    'gage' => $gage,
                    'dot' => array_search($item->id, $dateDots[$line][$dotWeek]),
                ];
//            echo $line .'-' . $dotWeek; var_dump($eventLines);
            }
        }
//dd($eventLines, $dateDots);

        $this->data['calendar'] = $calendar;
        $this->data['eventLines'] = $eventLines;
        $this->data['urlHandler'] = $urlHandler;
        $this->data['calendarMonth'] = date('Y-m', $standardTime);
        $this->data['calendarPrevMonth'] = date('Y-m', strtotime('-1 month', $standardTime));
        $this->data['calendarNextMonth'] = date('Y-m', strtotime('+1 month', $standardTime));
        $this->data['calendarPrevYear'] = date('Y-m', strtotime('-1 year', $standardTime));
        $this->data['calendarNextYear'] = date('Y-m', strtotime('+1 year', $standardTime));
    }
}
