<?php
namespace Xpressengine\Plugins\OSSBoardSkins\Components\CategoryTab;

use Xpressengine\Plugins\OSSBoardSkins\Components\OSS\OSSSkin;
use Xpressengine\Plugins\Board\Models\Board;
use XeSkin;
use Event;
use Auth;
use Gate;
use Xpressengine\Category\Models\Category;

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

        if (in_array($this->view, ['index', 'show'])) {
            $this->data['categoryTabs'] = $this->categories();
        }

        return parent::render();
    }

    protected function categories()
    {
        $configHandler = app('xe.board.config');
        $config = $configHandler->get($this->data['instanceId']);
        $items = [];
        if ($config->get('category') === true) {
            $categoryItems = Category::find($config->get('categoryId'))->items;


            foreach ($categoryItems as $categoryItem) {
                $model = Board::division($this->data['instanceId']);
                $query = $model->where('instance_id', $this->data['instanceId'])->visible();
                $query->leftJoin(
                    'board_category',
                    sprintf('%s.%s', $query->getQuery()->from, 'id'),
                    '=',
                    sprintf('%s.%s', 'board_category', 'target_id')
                );
                $query->where('item_id', $categoryItem->id);
                $count = $query->count();

                $items[] = [
                    'value' => $categoryItem->id,
                    'text' => $categoryItem->word,
                    'count' => $count,
                ];
            }
        }

        return $items;
    }
}
