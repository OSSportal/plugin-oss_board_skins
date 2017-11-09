<?php
namespace Xpressengine\Plugins\OSSBoardSkins\Components\FAQ;

use Xpressengine\Config\ConfigEntity;
use Xpressengine\Database\Eloquent\Builder;
use Xpressengine\Http\Request;
use Xpressengine\Permission\Instance;
use Xpressengine\Plugins\Board\Components\Modules\BoardModule;
use Xpressengine\Plugins\Board\Handler as BoardHandler;
use Xpressengine\Plugins\Board\Components\Skins\Board\Common\CommonSkin;
use Xpressengine\Routing\InstanceConfig;
use XeSkin;
use Event;
use Auth;
use Gate;

class FAQSkin extends CommonSkin
{
    protected static $path = 'oss_board_skins/components/FAQ';

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
        $this->data['_parentSkinPath'] = parent::$path;
        $this->data['withoutList'] = true;

        $boardPermission = app('Xpressengine\Plugins\Board\BoardPermissionHandler');
        $this->data['createPermission'] = Gate::allows(
            $boardPermission::ACTION_CREATE,
            new Instance($boardPermission->name($this->data['instanceId']))
        );
        return parent::render();
    }

    /**
     * skin 설정할 때 thumbnail table 을 join 할 수 있도록 intercept 등록
     *
     * @return void
     */
    protected static function interceptSetSkinTargetId()
    {
        // 기본 제공되는 정렬 사용하지 않고 별도의 기능 사용
        intercept(
            sprintf('%s@makeOrder', BoardHandler::class),
            'oss_board_skins::faq.make_order',
            function ($func, Builder $query, Request $request, ConfigEntity $config) {
                $instanceConfig = InstanceConfig::instance();

                if ($request instanceof Request) {
                    $isMobile = $request->isMobile();
                } else {
                    $isMobile = false;
                }

                $assignedSkin = XeSkin::getAssigned(
                    [BoardModule::getId(), $instanceConfig->getInstanceId()],
                    $isMobile ? 'mobile' : 'desktop'
                );

                // target 의 스킨이 현재 skin 의 아이디와 일치하는지 확인
                if ($assignedSkin->getId() == static::getId()) {
                    $query->getProxyManager()->orders($query->getQuery(), [
                        'order_num' => 'asc'
                    ]);
                } else {
                    $func($query, $request, $config);
                }
            }
        );
    }
}
