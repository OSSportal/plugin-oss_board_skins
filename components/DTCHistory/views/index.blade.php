{{ XeFrontend::js('assets/core/xe-ui-component/js/xe-page.js')->load() }}
{{ XeFrontend::css(\Xpressengine\Plugins\OSSBoardSkins\Plugin::asset('components/DTCHistory/assets/css/style.css'))->load() }}

<div class="board_header">
    @if ($isManager === true)
        <div class="bd_btn_area">
            <ul>
                <li><a href="{{ $urlHandler->get('create') }}"><span class="xe-sr-only">{{ xe_trans('board::newPost') }}</span><i class="xi-pen-o"></i></a></li>
            </ul>
        </div>
    @endif
    <div class="xe-form-inline xe-hidden-xs board-sorting-area __xe-forms">
    </div>
</div>

<div class="board_list">
    <!-- [D] 2019/07/24 - GNB 메뉴에 따라 탭 컬러 변경되어 적용된 클래스 class="board_tab_list_box_menu2" 두번째 탭이어서 2번 -->
    <div class="board_tab_list_box board_tab_list_box_menu2">
        <!-- [D] 2019/07/24 - 탭 노출 개수에 따라 클래스 적용 2 ~ 5까지 단수 적용 class="board_tab_list_type2" -->
        @php
            $totalCount = count($paginate);
            $tabCount = 3;
            if ($totalCount % 4 == 0) {
                $tabCount = 4;
            } elseif ($totalCount % 3 == 0) {
                $tabCount = 3;
            }
        @endphp
        <ul class="board_tab_list {{ 'board_tab_list_type' . $tabCount }}  ">
            @foreach($paginate as $item)
            <li class="__active_board">
                <a href="{{route('oss::union.getBoardContent', ['boardId' => $item->id])}}" id="title_{{$item->id}}" class="title_text board_tab_link"
                    data-toggle="xe-page"
                    data-target=".__board_content"
                >{!! $item->title !!}</a>

                @if($isManager == true)
                    <a href="{{ $urlHandler->get('edit', array_merge(Request::all(), ['id' => $item->id])) }}" class="bd_ico bd_modify"><i class="xi-eraser"></i><span class="xe-sr-only">{{ xe_trans('xe::update') }}</span></a>
                    <a href="#" class="bd_ico bd_delete" data-url="{{ $urlHandler->get('destroy', array_merge(Request::all(), ['id' => $item->id])) }}"><i class="xi-trash"></i><span class="xe-sr-only">{{ xe_trans('xe::delete') }}</span></a>
                @endif
            </li>
            @endforeach
        </ul>
    </div>

    <div class="read_body">
        <div class="xe_content">
            <div class="__xe_contents_compiler">
                <div class="__board_content">
                </div>
            </div>
        </div>
    </div>
</div>

<div class="board_footer">
    <!-- PAGINATAION PC-->
{!! $paginate->render('board::components.Skins.Board.Common.views.default-pagination') !!}
<!-- /PAGINATION PC-->

    <!-- PAGINATAION Mobile -->
{!! $paginate->render('board::components.Skins.Board.Common.views.simple-pagination') !!}
<!-- /PAGINATION Mobile -->
</div>
<div class="bd_dimmed"></div>

<script>
    $(function () {
        $('.board_tab_link').click(function () {
            $('.__active_board').removeClass('on');
            $(this).parents('li').addClass('on');
        });
    });
</script>
