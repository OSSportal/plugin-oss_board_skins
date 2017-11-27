<?php
namespace Xpressengine\Plugins\OSSBoardSkins\Components\OSS;

use Xpressengine\Plugins\Board\Components\Skins\Board\Common\CommonSkin;
use XeSkin;
use Event;
use Auth;
use Gate;
use XeFrontend;
use Xpressengine\Presenter\Presenter;
use XePresenter;
use View;
use Xpressengine\Plugins\Board\BoardPermissionHandler;
use Xpressengine\Plugins\Board\Handler as BoardHandler;
use Xpressengine\Permission\Instance;
use Xpressengine\Routing\InstanceConfig;

class OSSSkin extends CommonSkin
{
    protected static $path = 'oss_board_skins/components/OSS';

    public static function boot()
    {
	static::interceptSetSkinTargetId();
    }

    /**
     * render
     *
     * @return \Illuminate\Contracts\Support\Renderable|string
     */
    public function render()
    {
        if (!in_array($this->view, ['index', 'show', 'create', 'edit'])) {
            return parent::render();
        }

        $this->setSkinConfig();
        $this->setDynamicFieldSkins();
        $this->setPaginationPresenter();
        $this->setBoardList();
        $this->setTerms();

        // 스킨 view(blade)파일이나 js 에서 사용할 다국어 정의
        XeFrontend::translation([
            'board::selectPost',
            'board::selectBoard',
            'board::msgDeleteConfirm',
        ]);

        // set skin path
        $this->data['_skinPath'] = static::$path;
        $this->data['isManager'] = $this->isManager();

if (isset($this->data['paginate'])) {
        $total = $this->data['paginate']->total();
        $currentPage = \Request::get('page', 1);
        $perPage = $this->data['config']->get('perPage');
        $startCount = $total - (($currentPage-1) * $perPage);
        $this->data['_startCount'] = $startCount;
}

        $boardPermission = app('Xpressengine\Plugins\Board\BoardPermissionHandler');
        $this->data['createPermission'] = Gate::allows(
            BoardPermissionHandler::ACTION_CREATE,
            new Instance($boardPermission->name($this->data['instanceId']))
        );

        /**
         * If view file is not 'index.blade.php' then change view path to CommonSkin's path.
         * CommonSkin extends by other Skins. Extended Skin can make just 'index.blade.php'
         * and other blade files will use to CommonSkin's blade files.
         */
	if (View::exists(sprintf('%s/views/%s', static::$path, $this->view)) == false) {
            static::$path = self::$path;
	}
/*
        if ($this->view != 'index') {
            static::$path = self::$path;
        }
*/
        $contentView = $this->render2();

        /**
         * If render type is not for Presenter::RENDER_CONTENT
         * then use CommonSkin's '_frame.blade.php' for layout.
         * '_frame.blade.php' has assets load script like js, css.
         */
        if (XePresenter::getRenderType() == Presenter::RENDER_CONTENT) {
            $view = $contentView;
        } else {
            // wrapped by _frame.blade.php
            $view = View::make(sprintf('%s/views/_frame', OSSSkin::$path), $this->data);
            $view->content = $contentView;
        }

        return $view;
    }

    /**
     * 스킨을 출력한다.
     * 만약 view 이름과 동일한 메소드명이 존재하면 그 메소드를 호출한다.
     *
     * @return Renderable|string
     */
    public function render2()
    {
        $view = $this->view;
        $method = ucwords(str_replace(['-', '_', '.'], ' ', $view));
        $method = lcfirst(str_replace(' ', '', $method));

        // for php7
        if ($method === 'list') {
            $method = 'listView';
        }

        if (method_exists($this, $method)) {
            return $this->$method($view);
        } else {
            return $this->renderBlade($view);
        }
    }

    /**
     * skin 설정할 때 thumbnail table 을 join 할 수 있도록 intercept 등록
     *
     * @return void
     */
    protected static function interceptSetSkinTargetId()
    {
        intercept(
            sprintf('%s@setSkinTargetId', Presenter::class),
            static::class.'-set_skin_target_id',
            function ($func, $skinTargetId) {
                $func($skinTargetId);
                if (!$skinTargetId) {
                    return;
                }

                $request = app('request');
                $instanceConfig = InstanceConfig::instance();

                if ($request instanceof Request) {
                    $isMobile = $request->isMobile();
                } else {
                    $isMobile = false;
                }
                $assignedSkin = XeSkin::getAssigned(
                    [$skinTargetId, $instanceConfig->getInstanceId()],
                    $isMobile ? 'mobile' : 'desktop'
                );

                // target 의 스킨이 현재 skin 의 아이디와 일치하는지 확인
                if (in_array($instanceConfig->getUrl(), ['dev_support_activities', 'dev_competition_activities', 'kosslab_project']) && $assignedSkin->getId() == static::getId()) {
			// check instance
			intercept(
			    sprintf('%s@getOrders', BoardHandler::class),
			    static::class.'-board-getOrders',
			    function ($func) {
				$orders = $func();
				$orders[] = ['value' => 'datahub_year', 'text' => '연도순'];
				return $orders;
			    }
			);

			intercept(
			    sprintf('%s@makeOrder', BoardHandler::class),
			    static::class.'-board-makeOrder',
			    function ($func, $query, $request, $config) {
				$query = $func($query, $request, $config);
				if ($request->get('order_type') == 'datahub_year') {
					$query->orderBy('datahub_year', 'desc')->orderBy('head', 'desc');
				}
				return $query;
			    }
			);
                }
            }
        );
    }
}
