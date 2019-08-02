{{ XeFrontend::js('assets/core/xe-ui-component/js/xe-page.js')->appendTo('body')->load() }}
{{ XeFrontend::css(\Xpressengine\Plugins\OSSBoardSkins\Plugin::asset('components/DTCOverview/assets/css/style.css'))->load() }}

@if ($isManager === true)
    <div class="board_header">
        <div class="bd_btn_area">
            <ul>
                <li><a href="{{ $urlHandler->get('create') }}"><span class="xe-sr-only">{{ xe_trans('board::newPost') }}</span><i class="xi-pen-o"></i></a></li>
                <li><a href="{{ $urlHandler->managerUrl('config', ['boardId'=>$instanceId]) }}" target="_blank"><span class="xe-sr-only">{{ xe_trans('xe::manage') }}</span><i class="xi-cog"></i></a></li>
            </ul>
        </div>

        <div class="xe-form-inline xe-hidden-xs board-sorting-area __xe-forms">
            @if($config->get('category') == true)
                {!! uio('uiobject/board@select', [
                'name' => 'category_item_id',
                'label' => xe_trans('xe::category'),
                'value' => Request::get('category_item_id'),
                'items' => $categories,
                ]) !!}
            @endif
        </div>
    </div>
@endif

<!-- [D] 2단 적용 시 class="g_col2--flex" 적용 -->
<div class="board_list v2 gallery g_col2 g_col2--flex">
    <ul>
        @foreach($paginate as $item)
            <li>
                <div class="thumb_area">
                    <a href="{{$urlHandler->getShow($item, Request::all())}}">
                        <div class="thumbnail-cover thumbnail-cover--scale" @if($item->board_thumbnail_path) style="background-image: url('{{ $item->board_thumbnail_path }}')" @endif></div>
                    </a>
                </div>
                <div class="cont_area">
                    @if (in_array('title', $skinConfig['listColumns']) == true)
                        <a class="title" href="{{$urlHandler->getShow($item, Request::all())}}" id="title_{{$item->id}}">
                            {!! $item->title !!}
                        </a>
                    @endif

                    <div class="more_info">
                        @if (in_array('overview_add', $skinConfig['listColumns']) == true)
                            {!! $item->overview_add_text !!}
                        @endif
                    </div>
                </div>
            </li>
        @endforeach
    </ul>
</div>
