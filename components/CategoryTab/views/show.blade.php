{{ XeFrontend::js('/assets/vendor/jqueryui/jquery.event.drag-2.2.js')->load() }}
{{ XeFrontend::js('/assets/vendor/slickgrid/slick.core.js')->load() }}
{{ XeFrontend::js('/assets/vendor/slickgrid/slick.formatters.js')->load() }}
{{ XeFrontend::js('/assets/vendor/slickgrid/slick.grid.js')->load() }}
{{ XeFrontend::js('/assets/vendor/slickgrid/slick.dataview.js')->load() }}
{{ XeFrontend::css('/assets/vendor/slickgrid/slick.grid.css')->load() }}

{!! xe_trans($config->get('topViewContent', '')) !!}

<div class="board_read">
    <div class="read_header">
        @if($item->status == $item::STATUS_NOTICE)
            <span class="category">{{ xe_trans('xe::notice') }} @if($config->get('category') == true && $item->boardCategory !== null){{ xe_trans($item->boardCategory->getWord()) }}@endif</span>
        @elseif($config->get('category') == true && $item->boardCategory !== null)
            <span class="category">{{ xe_trans($item->boardCategory->getWord()) }}</span>
        @endif
        <h1><a href="{{ $urlHandler->getShow($item) }}">{!! $item->title !!}</a></h1>

        <div class="more_info">
            <!-- [D] 클릭시 클래스 on 적용 -->
            @if ($item->hasAuthor() && $config->get('anonymity') === false)
                <a href="{{ sprintf('/@%s', $item->getUserId()) }}" class="mb_autohr"
                   data-toggle="xe-page-toggle-menu"
                   data-url="{{ route('toggleMenuPage') }}"
                   data-data='{!! json_encode(['id'=>$item->getUserId(), 'type'=>'user']) !!}'>{{ $item->writer }}</a>
            @else
                <a class="mb_autohr">{{ $item->writer }}</a>
            @endif

            <span class="mb_time" title="{{$item->created_at}}"><i
                        class="xi-time"></i> <span>{{$item->created_at}}</span></span>
            <span class="mb_readnum"><i class="xi-eye"></i> {{$item->read_count}}</span>
        </div>
    </div>

    <div class="__xe_dynamicfield_group">
        @foreach ($skinConfig['formColumns'] as $columnName)
            @if (($fieldType = XeDynamicField::get($config->get('documentGroup'), $columnName)) != null && isset($dynamicFieldsById[$columnName]) && $dynamicFieldsById[$columnName]->get('use') == true)
                <div class="__xe_{{$columnName}} __xe_section">
                    {!! $fieldType->getSkin()->show($item->getAttributes()) !!}
                </div>
            @endif
        @endforeach

        @foreach ($fieldTypes as $dynamicFieldConfig)
            @if (in_array($dynamicFieldConfig->get('id'), $skinConfig['formColumns']) === false && ($fieldType = XeDynamicField::getByConfig($dynamicFieldConfig)) != null && $dynamicFieldConfig->get('use') == true)
                <div class="__xe_{{$dynamicFieldConfig->get('id')}} __xe_section">
                    {!! $fieldType->getSkin()->show($item->getAttributes()) !!}
                </div>
            @endif
        @endforeach
    </div>

    <div class="read_body">
        <div class="xe_content">
            {!! compile($item->instance_id, $item->content, $item->format === Xpressengine\Plugins\Board\Models\Board::FORMAT_HTML) !!}
        </div>
    </div>

    <div class="read_footer">
        @if (count($item->files) > 0)
            <div class="bd_file_list">
                <!-- [D] 클릭시 클래스 on 적용 -->
                <a href="#" class="bd_btn_file"><i class="xi-paperclip"></i><span
                            class="xe-sr-only">{{trans('board::fileAttachedList')}}</span> <strong
                            class="bd_file_num">{{ $item->data->file_count }}</strong></a>
                <ul style="display:block;">
                    @foreach($item->files as $file)
                        <li>
                            <a href="{{ route('editor.file.download', ['instanceId' => $item->instance_id, 'id' => $file->id])}}"><i
                                        class="xi-download"></i> {{ $file->clientname }} <span
                                        class="file_size">({{ bytes($file->size) }})</span></a></li>
                    @endforeach
                </ul>
            </div>
        @endif
        <div class="bd_function">
            <div class="bd_function_l">
                {!! uio('share', [
                    'item' => $item,
                    'url' => Request::url(),
                ]) !!}
            </div>

            <div class="bd_function_r">
                <a href="{{ $urlHandler->get('index', array_merge(Request::all())) }}" class="bd_ico bd_list"><i
                            class="xi-list"></i><span class="xe-sr-only">{{xe_trans('xe::list')}}</span></a>
                @if($isManager == true || $item->user_id == Auth::user()->getId() || $item->user_type === $item::USER_TYPE_GUEST)
                    <a href="{{ $urlHandler->get('edit', array_merge(Request::all(), ['id' => $item->id])) }}"
                       class="bd_ico bd_modify"><i class="xi-eraser"></i><span
                                class="xe-sr-only">{{ xe_trans('xe::update') }}</span></a>
                    <a href="#" class="bd_ico bd_delete"
                       data-url="{{ $urlHandler->get('destroy', array_merge(Request::all(), ['id' => $item->id])) }}"><i
                                class="xi-trash"></i><span class="xe-sr-only">{{ xe_trans('xe::delete') }}</span></a>
                @endif
                <div class="bd_more_area">
                    <!-- [D] 클릭시 클래스 on 적용 -->
                    <a href="#" class="bd_ico bd_more_view" title="추가기능" data-toggle="xe-page-toggle-menu"
                       data-url="{{route('toggleMenuPage')}}"
                       data-data='{!! json_encode(['id'=>$item->id,'type'=>'module/board@board','instanceId'=>$item->instance_id]) !!}'
                       data-side="dropdown-menu-right"><i class="xi-ellipsis-h"></i><span
                                class="xe-sr-only">{{ xe_trans('xe::more') }}</span></a>
                </div>
            </div>
            <div class="bd_like_more" id="bd_like_more{{$item->id}}" data-id="{{$item->id}}"></div>
        </div>
    </div>
</div>

<style>
    .xe-toggle-menu {
        min-width: 140px;
        padding: 8px 0;
        border: 1px solid #bebebe;
        border-radius: 4px;
        background-color: #fff;
        list-style: none;
    }

    .xe-toggle-menu li {
        height: 30px;
    }

    .xe-toggle-menu li > a {
        overflow: hidden;
        display: block;
        height: 100%;
        padding: 0 16px;
        font-size: 14px;
        line-height: 30px;
        color: #2c2e37;
    }

    .xe-toggle-menu li > a:hover {
        background-color: #f4f4f4;
    }
</style>
<!-- 댓글 -->
@if ($config->get('comment') === true && $item->boardData->allow_comment === 1)
    <div class="__xe_comment board_comment oss_board_comment">
        <a name="comment"></a>
        {!! uio('comment', ['target' => $item]) !!}
    </div>
@endif
<!-- // 댓글 -->

{!! xe_trans($config->get('bottomViewContent', '')) !!}

@if (isset($withoutList) === false || $withoutList === false)
    <!-- 리스트 -->
    @include($_skinPath.'/views/index')
@endif


