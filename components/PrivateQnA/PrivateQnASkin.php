<?php
namespace Xpressengine\Plugins\OSSBoardSkins\Components\PrivateQnA;

use Xpressengine\Http\Request;
use Xpressengine\Plugins\OSSBoardSkins\Components\OSS\OSSSkin;
use Xpressengine\Presenter\Presenter;
use Xpressengine\Routing\InstanceConfig;
use Xpressengine\Permission\Instance;
use XeSkin;
use Event;
use Auth;
use Gate;

class PrivateQnASkin extends OSSSkin
{
    protected static $path = 'oss_board_skins/components/PrivateQnA';

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
        /** @var \Xpressengine\Http\Request $request */
        $request = app('request');

        $this->data['_parentSkinPath'] = parent::$path;
        $this->data['withoutList'] = true;

        // 폼 출력을 기본으로 처리하도록 show, create, edit
//        $this->config['formColumns'] = parent::getSortFormColumns([], $this->data['instanceId']);

        return parent::render();
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
            'oss_board_skin::private_qna.set_skin_target_id',
            function ($func, $skinTargetId) {
                $func($skinTargetId);
                if (!$skinTargetId) {
                    return;
                }

                $request = app('request');
                $instanceConfig = InstanceConfig::instance();
                $instanceId = $instanceConfig->getInstanceId();
                if ($request instanceof Request) {
                    $isMobile = $request->isMobile();
                } else {
                    $isMobile = false;
                }
                $assignedSkin = XeSkin::getAssigned(
                    [$skinTargetId, $instanceId],
                    $isMobile ? 'mobile' : 'desktop'
                );

                // target 의 스킨이 현재 skin 의 아이디와 일치하는지 확인
                if ($assignedSkin->getId() == static::getId()) {
                    // 본인 글만 출력
                    Event::listen('xe.plugin.board.articles', function ($query) use ($instanceId) {
                        // check management grant
                        $boardPermission = app('xe.board.permission');
                        $isManager = Gate::allows(
                            $boardPermission::ACTION_MANAGE,
                            new Instance($boardPermission->name($instanceId))
                        ) ? true : false;

                        if ($isManager == false) {
                            $query->where('user_id', Auth::user()->getId());
                        } else {
                        }
                    });
                }
            }
        );
    }
}
