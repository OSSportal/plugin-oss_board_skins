{!! xe_trans($config->get('topViewContent', '')) !!}

<div class="board_read">
    @foreach ($skinConfig['formColumns'] as $columnName)
        @if($columnName === 'title')
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
                        <span class="xe-dropdown">
                                <a href="{{ sprintf('/@%s', $item->getUserId()) }}" class="mb_autohr"
                                   data-toggle="xe-page-toggle-menu"
                                   data-url="{{ route('toggleMenuPage') }}"
                                   data-data='{!! json_encode(['id'=>$item->getUserId(), 'type'=>'user']) !!}'>{{ $item->writer }}</a>
                           </span>
                    @else
                        <span>
                                <a class="mb_autohr">{{ $item->writer }}</a>
                            </span>
                    @endif

                    <span class="mb_time" title="{{$item->created_at}}"><i class="xi-time"></i> <span data-xe-timeago="{{$item->created_at}}">{{$item->created_at}}</span></span>
                    <span class="mb_readnum"><i class="xi-eye"></i> {{$item->read_count}}</span>
                </div>
            </div>
        @elseif($columnName === 'content')
            <div class="read_body">
                {{-- @DEPRECATED .xe_content --}}
                <div class="xe_content xe-content xe-content-{{ $item->instance_id }}">
                    {!! compile($item->instance_id, $item->content, $item->format === Xpressengine\Plugins\Board\Models\Board::FORMAT_HTML) !!}
                </div>
            </div>
        @elseif (($fieldType = XeDynamicField::get($config->get('documentGroup'), $columnName)) != null && isset($dynamicFieldsById[$columnName]) && $dynamicFieldsById[$columnName]->get('use') == true)
            <div class="__xe_{{$columnName}} __xe_section" style="border-bottom: 1px solid #e1e5e8;">
                {!! $fieldType->getSkin()->show($item->getAttributes()) !!}
            </div>
        @endif
    @endforeach

    @foreach ($fieldTypes as $dynamicFieldConfig)
        @if (in_array($dynamicFieldConfig->get('id'), $skinConfig['formColumns']) === false && ($fieldType = XeDynamicField::getByConfig($dynamicFieldConfig)) != null && $dynamicFieldConfig->get('use') == true)
            <div class="__xe_{{$columnName}} __xe_section">
                {!! $fieldType->getSkin()->show($item->getAttributes()) !!}
            </div>
        @endif
    @endforeach
    <div class="read_footer">
        @if (count($item->files) > 0)
            <div class="bd_file_list">
                <!-- [D] 클릭시 클래스 on 적용 -->
                <a href="#" class="bd_btn_file"><i class="xi-paperclip"></i><span class="xe-sr-only">{{trans('board::fileAttachedList')}}</span> <strong class="bd_file_num">{{ $item->data->file_count }}</strong></a>
                <ul>
                    @foreach($item->files as $file)
                        <li><a href="{{ route('editor.file.download', ['instanceId' => $item->instance_id, 'id' => $file->id])}}"><i class="xi-download"></i> {{ $file->clientname }} <span class="file_size">({{ bytes($file->size) }})</span></a></li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if ($isManager == true)
            <div class="bd_function">
                <div class="bd_function_r">
                    <a href="{{ $urlHandler->get('index', array_merge(Request::all())) }}" class="bd_ico bd_list"><i class="xi-list"></i><span class="xe-sr-only">{{xe_trans('xe::list')}}</span></a>
                    <a href="{{ $urlHandler->get('edit', array_merge(Request::all(), ['id' => $item->id])) }}" class="bd_ico bd_modify"><i class="xi-eraser"></i><span class="xe-sr-only">{{ xe_trans('xe::update') }}</span></a>
                    <a href="#" class="bd_ico bd_delete" data-url="{{ $urlHandler->get('destroy', array_merge(Request::all(), ['id' => $item->id])) }}"><i class="xi-trash"></i><span class="xe-sr-only">{{ xe_trans('xe::delete') }}</span></a>
                </div>
            </div>
        @endif
    </div>
</div>

<!-- 댓글 -->
@if ($config->get('comment') === true && $item->boardData->allow_comment === 1)
    <div class="__xe_comment board_comment oss_board_comment">
        {!! uio('comment', ['target' => $item]) !!}
    </div>
@endif
<!-- // 댓글 -->

{!! xe_trans($config->get('bottomViewContent', '')) !!}

<style>
    .xe-dynamicField label {
        display: table-cell;
    }
</style>
