<?php
namespace Xpressengine\Plugins\OSSBoardSkins\Components\OSSNoDF;

use Xpressengine\Plugins\OSSBoardSkins\Components\OSS\OSSSkin;
use XeSkin;
use Event;
use Auth;
use Gate;

class OSSNoDFSkin extends OSSSkin
{
    protected static $path = 'oss_board_skins/components/OSSNoDF';

    public static function boot()
    {
    }

    public function render()
    {
        $this->data['_parentSkinPath'] = parent::$path;
        return parent::render();
    }
}
