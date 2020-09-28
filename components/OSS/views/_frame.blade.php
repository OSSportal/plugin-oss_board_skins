<?php
use Xpressengine\Plugins\OSSBoardSkins\Components\OSS\OSSSkin;
?>
{{ XeFrontend::js('plugins/board/assets/js/board.js')->load() }}
{{ XeFrontend::css(OSSSkin::getPath() . '/assets/css/board.css')->load() }}

<style>
    .bd_function .bd_like.voted{color:#FE381E}
</style>

<!-- BOARD -->
<div class="board">
    @if ($config->get('topCommonContentOnlyList') === true)
        <div class="xe-list-board-header__text">
            {!! xe_trans($config->get('topCommonContent', '')) !!}
        </div>
    @endif
    
    @section('content')
        {!! isset($content) ? $content : '' !!}
    @show

    @if ($config->get('bottomCommonContentOnlyList') === true)
        <div class="xe-list-board-footer__text">
            {!! xe_trans($config->get('bottomCommonContent', '')) !!}
        </div>
    @endif
</div>
<!-- /BOARD -->
