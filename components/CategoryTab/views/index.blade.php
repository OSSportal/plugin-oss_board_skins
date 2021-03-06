@if(Request::segment(1) == 'info_techtip' || Request::segment(1) == 'info_sec')
<div class="intro_use info_techtip">
    <ul class="_oss_tab">
        @foreach ($categoryTabs as $categoryTab)
        <li @if ($categoryTab['value'] == Request::get('category_id')) class="on" @endif>
            <a href="{{$urlHandler->get('index', ['category_item_id' => $categoryTab['value']])}}">{{xe_trans($categoryTab['text'])}} ({{$categoryTab['count']}})</a>
        </li>
        @endforeach
    </ul>

</div>
@endif

@include($_parentSkinPath.'/views/index')
