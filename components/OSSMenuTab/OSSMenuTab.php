<?php

namespace Xpressengine\Plugins\OSSBoardSkins\Components\OSSMenuTab;

use Xpressengine\Menu\Models\MenuItem;
use Xpressengine\Plugins\OSSBoardSkins\Components\OSSNoDF\OSSNoDFSkin;

class OSSMenuTab extends OSSNoDFSkin
{
    protected static $path = 'oss_board_skins/components/OSSMenuTab';
    
    public function render()
    {
        $currentMenu = current_menu();
        $parentMenu = MenuItem::where('id', $currentMenu['parent_id'])->get()->first();
        $siblingMenus = $parentMenu->getChildren();
        
        $this->data['siblingMenus'] = $siblingMenus;
        
        return parent::render();
    }
}
