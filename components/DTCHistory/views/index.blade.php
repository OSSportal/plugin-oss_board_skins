{{ XeFrontend::js('assets/core/xe-ui-component/js/xe-page.js')->load() }}
{{ XeFrontend::css(\Xpressengine\Plugins\OSSBoardSkins\Plugin::asset('components/DTCHistory/assets/css/style.css'))->load() }}
{{ XeFrontend::css(\Xpressengine\Plugins\OSSBoardSkins\Plugin::asset('components/DTCOverview/assets/css/style.css'))->load() }}

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
            $totalCount = count($categoryTree);
            $tabCount = 3;
            if ($totalCount % 4 == 0) {
                $tabCount = 4;
            } elseif ($totalCount % 3 == 0) {
                $tabCount = 3;
            }
        @endphp
        
        <ul class="board_tab_list {{ 'board_tab_list_type' . $tabCount }}  ">
            @foreach ($categoryTree as $category)
                @php
                    $isActive = false;

                    if (Request::get('category_item_id') === (string)$category['value']) {
                        $isActive = true;
                    } else {
                        if (count($category['children']) > 0) {
                            $requestCategoryItem = Request::get('category_item_id');
                            foreach ($category['children'] as $childCategory) {
                                if ($requestCategoryItem === (string)$childCategory['value']) {
                                    $isActive = true;
                                    break;
                                }
                            }
                        }
                    }
                @endphp
                <li class="__active_board @if ($isActive === true) on @endif">
                    @if (count($category['children']) > 0)
                        <a href="#" class="title_text board_tab_link __category-link __has-child-category" data-category-value="{{ $category['value'] }}" onclick="return false;">{{ $category['text'] }}</a>
                    @else
                        <a href="{{ $urlHandler->get('index', ['category_item_id' => $category['value']]) }}" class="title_text __category-link board_tab_link">{{ $category['text'] }}</a>
                    @endif
                </li>
            @endforeach
        </ul>
        
        @foreach ($categoryTree as $category)
            @if (count($category['children']) > 0)
                @php
                    $isVisible = false;
        
                    foreach ($category['children'] as $childCategory) {
                        if (Request::get('category_item_id') === (string)$childCategory['value']) {
                            $isVisible = true;
                            break;
                        }
                    }
                @endphp
                
                <ul class="board_tab_list {{ 'board_tab_list_type' . count($category['children']) }} __child-category-list __child-category-{{ $category['value'] }}" style="margin-top: 20px; @if ($isVisible === false) display: none; @endif">
                    @foreach ($category['children'] as $childCategory)
                        <li @if (Request::get('category_item_id') === (string)$childCategory['value']) class="on" @endif>
                            <a href="{{ $urlHandler->get('index', ['category_item_id' => $childCategory['value']]) }}" class="title_text board_tab_link">{{ $childCategory['text'] }}</a>
                        </li>
                    @endforeach
                </ul>
            @endif
        @endforeach
    </div>

    @if ($firstItem !== null)
        <div class="read_body">
            <div class="xe_content">
                {!! compile($firstItem->instance_id, $firstItem->content, $firstItem->format === Xpressengine\Plugins\Board\Models\Board::FORMAT_HTML) !!}
            </div>

            @if($isManager == true)
                <div>
                    <p>관리자 메뉴</p>
                    <a href="{{ $urlHandler->get('edit', array_merge(Request::all(), ['id' => $firstItem->id])) }}" class="bd_ico bd_modify"><i class="xi-eraser"></i><span class="xe-sr-only">{{ xe_trans('xe::update') }}</span></a>
                    <a href="#" class="bd_ico bd_delete" data-url="{{ $urlHandler->get('destroy', array_merge(Request::all(), ['id' => $firstItem->id])) }}"><i class="xi-trash"></i><span class="xe-sr-only">{{ xe_trans('xe::delete') }}</span></a>
                </div>
            @endif
        </div>
    @else
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
    @endif
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
        $('.__category-link').click(function () {
            $('.__child-category-list').css('display', 'none')
            $('.__active_board').removeClass('on')
        })
        
        $('.__has-child-category').click(function () {
            $(this).closest('li').addClass('on')
            
            $('.__child-category-' + $(this).data('category-value')).css('display', 'block')
        })
    });
</script>
