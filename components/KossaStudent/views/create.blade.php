{{ XeFrontend::rule('board', $rules) }}
{{ XeFrontend::js('assets/core/common/js/draft.js')->load() }}
{{ XeFrontend::css('assets/core/common/css/draft.css')->load() }}
{{ XeFrontend::js('plugins/board/assets/js/BoardTags.js')->load() }}
{{ XeFrontend::css(\Xpressengine\Plugins\OSSBoardSkins\Plugin::asset('components/KossaStudent/assets/css/style.css'))->load() }}

@if ($isManager == true)
    <div class="pull-right" style="padding-bottom: 20px;">
        <a href="https://oss.kr/student_apply" class="xe-btn xe-btn-danger">신청 목록으로 이동</a>
    </div>
@endif

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

        <p style="display: block; margin: 0 0 20px; color: #ff0000;">
            ▶ 참가신청 방법 <br/>
            개최대학, 실습교육 장소는 추후 게시판을 통해 공지할 예정입니다.<br/>
            튜토리얼은 "대학생 공개SW 체험캠프"중 진행되는 실습형 교육입니다. 본인의 관심분야를 선택해 주세요.<br/>
            프로젝트별 인원 제한이 있으니 참고하시기 바랍니다.<br/>
            ※ 인원초과일 경우 다른 프로젝트로 배정될 수 있음
        </p>

        <div class="__xe_dynamicfield_group">
            @foreach ($skinConfig['formColumns'] as $columnName)
                @if(isset($dynamicFieldsById[$columnName]) && $dynamicFieldsById[$columnName]->get('use') == true)
                    <div class="__xe_{{$columnName}} __xe_section">
                        {!! df_create($config->get('documentGroup'), $columnName, Request::all()) !!}
                    </div>
                @endif
            @endforeach
            @foreach ($fieldTypes as $dynamicFieldConfig)
                @if (in_array($dynamicFieldConfig->get('id'), $skinConfig['formColumns']) === false && ($fieldType = XeDynamicField::getByConfig($dynamicFieldConfig)) != null && $dynamicFieldConfig->get('use') == true)
                    <div class="__xe_{{$dynamicFieldConfig->get('id')}} __xe_section">
                        {!! df_create($dynamicFieldConfig->get('group'), $dynamicFieldConfig->get('id'), Request::all()) !!}
                    </div>
                @endif
            @endforeach
        </div>

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
                <span class="xe-btn-group">
                    <button type="button"
                            class="xe-btn xe-btn-secondary __xe_temp_btn_save">{{ xe_trans('xe::draftSave') }}</button>
                    <button type="button" class="xe-btn xe-btn-secondary xe-dropdown-toggle" data-toggle="xe-dropdown"
                            aria-haspopup="true" aria-expanded="false">
                        <span class="caret"></span>
                        <span class="xe-sr-only">Toggle Dropdown</span>
                    </button>
                    <ul class="xe-dropdown-menu">
                        <li><a href="#" class="__xe_temp_btn_load">{{ xe_trans('xe::draftLoad') }}</a></li>
                    </ul>
                </span>
                <button type="button" class="bd_btn btn_preview __xe_btn_preview">{{ xe_trans('xe::preview') }}</button>
                <button type="submit" class="bd_btn btn_submit __xe_btn_submit">{{ xe_trans('xe::submit') }}</button>
            </div>
        </div>
    </form>
</div>

<script type="text/javascript">
    $(function () {
        var form = $('#board_form');
        var draft = $('#xeContentEditor', form).draft({
            key: 'document|' + form.data('instance_id'),
            btnLoad: $('.__xe_temp_btn_load', form),
            btnSave: $('.__xe_temp_btn_save', form),
            withForm: true,
            apiUrl: {
                draft: {
                    add: xeBaseURL + '/draft/store',
                    update: xeBaseURL + '/draft/update',
                    delete: xeBaseURL + '/draft/destroy',
                    list: xeBaseURL + '/draft',
                },
                auto: {
                    set: xeBaseURL + '/draft/setAuto',
                    unset: xeBaseURL + '/draft/destroyAuto'
                }
            },
            callback: function (data) {
                window.XE.app('Editor').then(function (appEditor) {
                    appEditor.getEditor('XEckeditor').then(function (editorDefine) {
                        var inst = editorDefine.editorList['xeContentEditor']
                        if (inst) {
                            inst.setContents(data.content);
                        }
                    })
                })
            }
        });

        $('input[name=title]').attr('placeholder', '[대학명] 참여지원서 제출합니다.');
    });
</script>
