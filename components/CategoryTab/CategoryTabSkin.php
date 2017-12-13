<?php
namespace Xpressengine\Plugins\OSSBoardSkins\Components\CategoryTab;

use Xpressengine\Plugins\OSSBoardSkins\Components\OSS\OSSSkin;
use Xpressengine\Plugins\Board\Models\Board;
use XeSkin;
use Event;
use Auth;
use Gate;
use Xpressengine\Category\Models\CategoryItem;

class CategoryTabSkin extends OSSSkin 
{
    protected static $path = 'oss_board_skins/components/CategoryTab';

    public static function boot()
    {
    }

    /**
     * render
     *
     * @return \Illuminate\Contracts\Support\Renderable|string
     */
    public function render()
    {
        /** @var \Xpressengine\Http\Request $request */
        $request = app('request');

        $this->data['_parentSkinPath'] = parent::$path;

        return parent::render();
    }

}
