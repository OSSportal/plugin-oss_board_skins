{{ XeFrontend::rule('board', $rules) }}
{{ XeFrontend::js('assets/core/common/js/draft.js')->load() }}
{{ XeFrontend::js('plugins/board/assets/js/BoardTags.js')->load() }}
{{ XeFrontend::css(\Xpressengine\Plugins\OSSBoardSkins\Plugin::asset('components/DTCApply/assets/css/style.css'))->load() }}

<div class="board_write">
    <form method="post" id="board_form" class="__board_form" action="{{ $urlHandler->get('store') }}"
          enctype="multipart/form-data" data-rule="board" data-rule-alert-type="toast"
          data-instance_id="{{$instanceId}}" data-url-preview="{{ $urlHandler->get('preview') }}">
        <input type="hidden" name="_token" value="{{{ Session::token() }}}"/>
        <input type="hidden" name="head" value="{{$head}}"/>
        <input type="hidden" name="queryString" value="{{ http_build_query(Request::except('parent_id')) }}"/>

        <div class="write_header">
            <div class="write_title">
                {!! uio('titleWithSlug', [
                'title' => Request::old('title'),
                'slug' => Request::old('slug'),
                'titleClassName' => 'bd_input',
                'config' => $config
                ]) !!}
            </div>
        </div>

{{--        <div class="__xe_dynamicfield_group">--}}
{{--            @foreach ($skinConfig['formColumns'] as $columnName)--}}
{{--                @if(isset($dynamicFieldsById[$columnName]) && $dynamicFieldsById[$columnName]->get('use') == true)--}}
{{--                    <div class="__xe_{{$columnName}} __xe_section">--}}
{{--                        {!! df_create($config->get('documentGroup'), $columnName, Request::all()) !!}--}}
{{--                    </div>--}}
{{--                @endif--}}
{{--            @endforeach--}}
{{--            @foreach ($fieldTypes as $dynamicFieldConfig)--}}
{{--                @if (in_array($dynamicFieldConfig->get('id'), $skinConfig['formColumns']) === false && ($fieldType = XeDynamicField::getByConfig($dynamicFieldConfig)) != null && $dynamicFieldConfig->get('use') == true)--}}
{{--                    <div class="__xe_{{$dynamicFieldConfig->get('id')}} __xe_section">--}}
{{--                        {!! df_create($dynamicFieldConfig->get('group'), $dynamicFieldConfig->get('id'), Request::all()) !!}--}}
{{--                    </div>--}}
{{--                @endif--}}
{{--            @endforeach--}}
{{--        </div>--}}

        <p style="display: block; margin: 0 0 20px; color: #ff0000;">
            참가신청 방법 : 첨부파일을 다운로드 받아서 작성 후 업로드 해주시길 바랍니다.<br/>
            -파일명 : 1지망 참여희망 프로젝트_제출일자 예)공개SW컨트리뷰톤_190805.docx
        </p>

        <div class="write_body">
            <div class="write_form_editor">
                {!! editor($config->get('boardId'), [
                  'content' => Request::old('content'),
                ]) !!}
            </div>
        </div>

        <!-- 비로그인 -->
        <div class="write_footer">
            <div class="write_form_input">
                @if (Auth::check() === false)
                    <p style="display: block; margin: 0 0 20px; color: #ff0000;">
                        공개SW포털에 회원 가입을 하지 않고 신청이 가능합니다.<br/>
                        비회원으로 신청할 경우 아래 정보를 입력해주세요.<br/>
                        참가 신청 직후 신청 내역 확인 시 아래 정보로 확인 가능합니다.<br/>
                        신청 내용의 수정을 원하시는 경우 <a href="https://www.oss.kr/contributhon_qna/show/11e2ddfd-4021-4e33-aac9-a3287b4fe074" target="_blank">FAQ</a> 게시물을 참고해주세요.
                    </p>

                    <div class="xe-form-inline">
                        <input type="text" name="writer" class="xe-form-control"
                               placeholder="{{ xe_trans('xe::writer') }}" title="{{ xe_trans('xe::writer') }}"
                               value="{{ Request::old('writer') }}">
                        <input type="password" autocomplete=”off” autocomplete=”off” name="certify_key" class="xe-form-control"
                               placeholder="{{ xe_trans('xe::password') }}" title="{{ xe_trans('xe::password') }}">
                        <input type="email" name="email" class="xe-form-control"
                               placeholder="{{ xe_trans('xe::email') }}" title="{{ xe_trans('xe::email') }}"
                               value="{{ Request::old('email') }}">
                    </div>
                @endif
            </div>

            <div class="write_form_option" style="display: none;">
                <div class="xe-form-inline">
                    <label class="xe-label">
                        <input type="checkbox" name="display"
                               value="{{\Xpressengine\Document\Models\Document::DISPLAY_SECRET}}" checked>
                        <span class="xe-input-helper"></span>
                        <span class="xe-label-text">{{xe_trans('board::secretPost')}}</span>
                    </label>
                </div>
            </div>

            <div class="write_form_btn @if (Auth::check() === false) nologin @endif">
                <button type="button" class="bd_btn btn_preview __xe_btn_preview">{{ xe_trans('xe::preview') }}</button>
                <button type="submit" class="bd_btn btn_submit __xe_btn_submit">{{ xe_trans('xe::submit') }}</button>
            </div>
        </div>
    </form>
</div>

<script type="text/javascript">
    $(function () {
        $('input[name=title]').attr('placeholder', '[프로젝트명] 참가신청서 제출합니다');
    });
</script>
