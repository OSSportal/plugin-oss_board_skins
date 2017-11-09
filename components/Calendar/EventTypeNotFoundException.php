<?php
namespace Xpressengine\Plugins\OSSBoardSkins\Components\Calendar;

use Xpressengine\Support\Exceptions\HttpXpressengineException;

class EventTypeNotFoundException extends HttpXpressengineException
{
    protected $statusCode = 500;
}
