{{ XeFrontend::css('assets/vendor/jqueryui/jquery-ui.min.css')->load() }}
{{ XeFrontend::js('assets/vendor/jqueryui/jquery-ui.min.js')->appendTo('head')->load() }}
<div class="event_cbtn">

    @if ($createPermission)
        <a href="{{ $urlHandler->get('create') }}"><span>{{ xe_trans('board::newPost') }}</span><i class="xi-pen-o"></i></a>
    @endif
    <a href="/event?listStyle=calendar" class="view_calendar">달력</a>
    <a href="/event?listStyle=list" class="view_list">리스트</a>
</div>



@if ($listStyle == 'calendar')
    <style>
        .board td {
            width: inherit !important;
        }

        .ui-datepicker-calendar {
            display: none;
        }

        .e_calendar {
            width: 100%;
            border: 1px solid #e1e5e8;
            border-collapse: collapse;
            box-sizing: content-box;
            text-align: center;
        }

        .e_calendar th, td {
            width: calc(100% / 7);
            border: 1px solid #e1e5e8;
            border-collapse: collapse;
            box-sizing: content-box;
            padding: 10px 0;
        }

        .e_calendar .e_date td {
            font-weight: bold;
        }

        .e_calendar .e_date td span {
            display: block;
            font-weight: normal;
        }

        .e_calendar .e_date .sun {
            color: #f2412c;
        }

        .e_calendar .e_date .sun span {
            color: #f2412c;
        }

        .e_calendar .e_txt td {
            position: relative;
            height: 110px;
        }

        .e_calendar .e_txt div {
            position: absolute;
            top: 12px;
            z-index: 15;
        }

        .e_calendar .e_txt p {
            height: 28px;
            margin-bottom: 1px;
            overflow: hidden;
        }

        .e_calendar .e_txt a {
            font-size: 13px;
            line-height: 18px;
            font-weight: bold;
        }

        .e_calendar .day_1 {
            width: 100%;
        }

        .e_calendar .day_2 {
            width: 191%;
        }

        .e_calendar .day_3 {
            width: 302%;
        }

        .e_calendar .day_4 {
            width: 403%;
        }

        .e_calendar .day_5 {
            width: 504%;
        }

        .e_calendar .day_6 {
            width: 605%;
        }

        .e_calendar .day_7 {
            width: 706%;
        }

        .not_now_day {
            -ms-filter: 'progid:DXImageTransform.Microsoft.Alpah(Opacity=30)'; /*IE8 */
            filter: alpha(opactiy=30); /* IE5~7, 8~9 */
            opacity: 0.3;
        }
    </style>


    <div class="ec_page">
        <form class="filter">
            <input type="hidden" name="listStyle" value="calendar"/>
            <button type="button" class="btn-month" data-month="{{$calendarPrevMonth}}"><img
                        src="/plugins/oss/components/Themes/OSS/assets/images/arr_left.gif" width="8" height="13">
            </button>
            <input type="text" name="calendar_month" value="{{$calendarMonth}}">
            <button type="button" class="btn-month" data-month="{{$calendarNextMonth}}"><img
                        src="/plugins/oss/components/Themes/OSS/assets/images/arr_right.gif" width="8" height="13">
            </button>
        </form>
    </div>

    <table class="e_calendar">
        @foreach ($calendar as $line => $dates)
            @if($line == 0)
                <tr class="e_week">
                    @foreach ($dates as $date)
                        <td @if(date('w', $date['time']) == 0) class="sun" @endif >
                            <span>{{date('D', $date['time'])}}</span></td>
                    @endforeach
                </tr>
            @endif
            <tr class="e_date">
                @foreach ($dates as $date)
                    <td @if(date('w', $date['time']) == 0) class="sun"
                        @endif style="text-align:left;padding-left:10px;">
                        @if(substr($date['date'], 5, 2) == substr($calendarMonth, 5))
                            {{substr($date['date'], 8)}}
                        @else
                            <span class="not_now_day">{{substr($date['date'], 8)}} </span>
                        @endif
                    </td>
                @endforeach
            </tr>

            <tr class="e_txt">
                @foreach ($eventLines[$line] as $week => $item)
                    <td>
                        @foreach ($item as $data)
                            @if($data['dot'] == 0)
                                <div class="clr day_{{$data['gage']}}" style="top: 12px; ">
                                    <p class=""
                                       style="background:{{$data['item']->seminar_mark_color ? $data['item']->seminar_mark_color : '#eee'}};">
                                        <a href="{{$urlHandler->getShow($data['item'], Request::all())}}"
                                           title="{{strip_tags(html_entity_decode($data['item']->title))}}">{{strip_tags(html_entity_decode($data['item']->title))}}</a></p>
                                </div>
                            @elseif($data['dot'] == 1)
                                <div class="clr day_{{$data['gage']}}" style="top: 41px;">
                                    <p class=""
                                       style="background:{{$data['item']->seminar_mark_color ? $data['item']->seminar_mark_color : '#eee'}};">
                                        <a href="{{$urlHandler->getShow($data['item'], Request::all())}}"
                                           title="{{strip_tags(html_entity_decode($data['item']->title))}}">{{strip_tags(html_entity_decode($data['item']->title))}}</a></p>
                                </div>
                            @elseif($data['dot'] == 2)
                                <div class="clr day_{{$data['gage']}}" style="top: 70px;">
                                    <p class=""
                                       style="background:{{$data['item']->seminar_mark_color ? $data['item']->seminar_mark_color : '#eee'}};">
                                        <a href="{{$urlHandler->getShow($data['item'], Request::all())}}"
                                           title="{{strip_tags(html_entity_decode($data['item']->title))}}">{{strip_tags(html_entity_decode($data['item']->title))}}</a></p>
                                </div>
                            @endif
                        @endforeach
                    </td>
                @endforeach
            </tr>
        @endforeach
    </table>
    <script>
        $(function () {
            var options = {
                changeMonth: true,
                changeYear: true,
                showButtonPanel: true,
                dateFormat: 'yy-mm',
                defaultDate: new Date({{substr($calendarMonth, 0, 4)}}, {{(int)substr($calendarMonth, 5, 2)}}, 1),
                onSelect: function (dateText) {
                    $('.filter').submit();
                },
                onClose: function (dateText, inst) {
                    $(this).datepicker('setDate', new Date(inst.selectedYear, inst.selectedMonth, 1));
                    $('.filter').submit();
                }
            };

            $('input[name="calendar_month"]').datepicker(options);

            $('.btn-month').bind('click', function (event) {
                $('input[name="calendar_month"]').val($(this).data('month'));
                $('.filter').submit();
            });
        });
    </script>
@else
    @include($_originSkinPath.'/views/origin_index')
@endif
