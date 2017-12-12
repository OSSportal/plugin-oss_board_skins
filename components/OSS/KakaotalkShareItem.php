<?php
namespace Xpressengine\Plugins\OSSBoardSkins\Components\OSS;

use Xpressengine\ToggleMenu\AbstractToggleMenu;

class KakaotalkShareItem extends AbstractToggleMenu
{
    /**
     * 메뉴에서 보여질 문자열
     *
     * @return string
     */
    public function getText()
    {
        return '카카오톡';
    }

    /**
     * get type
     *
     * @return string
     */
    public function getType()
    {
        return static::MENUTYPE_RAW;
    }

    /**
     * get action url
     *
     * @return string
     */
    public function getAction()
    {
	$url = '';
        return '<a href="#" class="share-item share-item-kakaotalk" data-url="'.$url.'" data-type="kakaotalk"><i class="xi-kakaotalk"></i>'
        .$this->getText().'</a>';
    }

    /**
     * 별도의 js 파일을 load 해야 하는 경우 해당 파일의 경로
     * 없는 경우 null 반환
     *
     * @return string|null
     */
    public function getScript()
    {
        return null;
    }

/**
 * mobile 에서만 사용
*/
	public function allows()
	{
		return \Request::isMobile();
	}
}

