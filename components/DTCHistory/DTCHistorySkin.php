<?php

namespace Xpressengine\Plugins\OSSBoardSkins\Components\DTCHistory;

use Xpressengine\Category\Models\CategoryItem;
use Xpressengine\Config\ConfigEntity;
use Xpressengine\Plugins\Board\Components\Skins\Board\Common\CommonSkin;

class DTCHistorySkin extends CommonSkin
{
    protected static $path = 'oss_board_skins/components/DTCHistory';

    public function render()
    {
        $this->data['firstItem'] = null;
        
        $this->data['categoryTree'] = $this->getCategoryItemsTree($this->data['config']);

        if ($this->view === 'index' && request()->get('category_item_id', null) !== null) {
            if ($this->data['paginate']->count() === 1) {
                $firstItem = $this->data['paginate']->first();
                $this->data['firstItem'] = $firstItem;
            }
        }
        
        return parent::render();
    }

    public function getCategoryItemsTree(ConfigEntity $config)
    {
        $items = [];
        if ($config->get('category') === true) {
            $categoryItems = CategoryItem::where('category_id', $config->get('categoryId'))
                ->where('parent_id', null)
                ->orderBy('ordering')->get();

            foreach ($categoryItems as $categoryItem) {
                $categoryItemData = [
                    'value' => $categoryItem->id,
                    'text' => xe_trans($categoryItem->word),
                    'children' => $this->getCategoryItemChildrenData($categoryItem)
                ];
                
                $items[] = $categoryItemData;
            }
        }

        return $items;
    }

    private function getCategoryItemChildrenData(CategoryItem $categoryItem)
    {
        $children = $categoryItem->getChildren();

        if ($children->isEmpty() === true) {
            return [];
        }

        $childrenData = [];
        foreach ($children as $child) {
            $childrenData[] = [
                'value' => $child->id,
                'text' => xe_trans($child->word),
                'children' => $this->getCategoryItemChildrenData($child)
            ];
        }

        return $childrenData;
    }
}
