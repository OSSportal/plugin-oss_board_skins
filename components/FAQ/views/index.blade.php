<style>
    .intro_faq .faq-list img {max-width: 100%;}
    .intro_faq .btn-faq__board-update {float: none; display: inline-block; width: 140px; height: 40px; margin-left: 8px; padding: 5px 0; font-size: 14px; vertical-align: top; text-align: center; color: #000;}
</style>
<div class="intro_faq">

    @if ($createPermission)
        <a href="{{ $urlHandler->get('create') }}"><span class="xe-sr-only">{{ xe_trans('board::newPost') }}</span><i class="xi-pen-o"></i></a>
    @endif

    <div class="clr">
        <a href="#" class="faq_01 faq-intro" data-title="공개SW란 무엇인가?"><span>공개SW란</span>무엇인가?</a>
        <a href="#" class="faq_02 faq-intro" data-title="4차 산업혁명시대에 공개SW가 각광받는 이유는?">4차<br />산업혁명시대에<span>공개SW가<br /> 각광받는</span>이유는?</a>
        <a href="#" class="faq_03 faq-intro" data-title="공개SW 도입 성공사례가 없다?"><span>공개SW 도입</span>성공사례가 없다?</a>
    </div>

    <ul class="faq-list">
        @foreach($paginate as $item)
        <li class="faq-item">
            <a href="#" class="clr">
                @if ($item->order_num == 0)
                    <span class="left">Intro.</span>
                @else
                    <span class="left">Question. {{$item->order_num}}</span>
                @endif
                <span class="right faq-title" data-title="{{$item->title}}">{{$item->title}}</span>

            </a>
            <div class="faq-content" style="display:none;">
                {!! compile($item->instance_id, $item->content, $item->format === Xpressengine\Plugins\Board\Models\Board::FORMAT_HTML) !!}
                <span class="btn btn-fold">
                    <button>접기</button>
                    @if ($createPermission)
                        <a href="{{ $urlHandler->get('edit', ['id' => $item->id]) }}" class="btn-faq__board-update">수정하기</a>
                    @endif
                </span>
            </div>
        </li>
        @endforeach
    </ul>

</div>

<script>
    $(function () {
        $('.btn-fold').bind('click', function () {
            $('.faq-list li').removeClass('on').find('');
            $('.faq-list li .faq-content').hide();
        });
        $('.faq-title').bind('click', function (event) {
            event.preventDefault();
            $('.faq-list li').removeClass('on');
            $('.faq-list li .faq-content').hide();

            $(this).closest('.faq-item').addClass('on').find('.faq-content').show();
        });
	$('.faq-intro').bind('click', function (event) {
            event.preventDefault();
	    var title = $(this).data('title');
	     $('.faq-list li').find('[data-title="'+title+'"]').trigger('click');
	});
    });
</script>
