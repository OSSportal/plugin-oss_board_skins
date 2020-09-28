<?php
namespace Xpressengine\Plugins\OSSBoardSkins;

use Xpressengine\Plugin\AbstractPlugin;

class Plugin extends AbstractPlugin
{
    /**
     * 이 메소드는 활성화(activate) 된 플러그인이 부트될 때 항상 실행됩니다.
     *
     * @return void
     */
    public function boot()
    {
        //test
		intercept(
			\Xpressengine\Plugins\Board\Validator::class . '@getCreateRule', 
			'oss.board-getCreateRule', 
			function ($func, $user, $config, $rules = null) {
				if ($config->get('useTitleHead', false) == true) {
					$rules = [
						'title_head' => 'required'
					];
				}
				$result = $func($user, $config, $rules);
				return $result;
			}
		);
		
		intercept(
			\Xpressengine\Plugins\Board\Validator::class . '@getEditRule', 
			'oss.board-getEditRule', 
			function ($func, $user, $config, $rules = null) {
				if ($config->get('useTitleHead', false) == true) {
					$rules = [
						'title_head' => 'required'
					];
				}
				$result = $func($user, $config, $rules);
				return $result;
			}
		);
		
    }
}
