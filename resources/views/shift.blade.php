@extends('layouts.front-main')

@section('page-css')
    <link rel="stylesheet" href="/css/shift.css?2022030801">
@endsection

@section('page-js')
    <script>
        $(function () {
            /* -------------------------------------
             登録ボタン
            ------------------------------------- */
            $(document).on('click', '.time a', function () {
                $('#delete-bt-box').hide();
                let staff_id = $(this).data('staff_id');
                let shift_date = $(this).data('date');
                let date_list = shift_date.split('-');

                $('#shift-form input[name="staff_id"]').val(staff_id);
                $('#shift-form #title').text(date_list[0] + '年' + date_list[1] + '月' + date_list[2] + '日');
                $('#shift-form input[name="date"]').val(shift_date);
                $('#shift-form select[name="start_h"]').val('08');
                $('#shift-form select[name="start_m"]').val('00');
                $('#shift-form select[name="end_h"]').val('08');
                $('#shift-form select[name="end_m"]').val('00');
                $('#shift-form input[name="type"]').val('register');
                $('#shift-modal h1').text('時間登録');
                $('#shift-form input[name="_method"]').val('');
                $('#shift-modal').remodal().open();
                return false;
            });

            /* -------------------------------------
             編集ボタン
            ------------------------------------- */
            $(document).on('click', '.modify-bt', function () {
                $('#delete-bt-box').show();
                let start_h = $(this).data('start_h');
                let start_m = $(this).data('start_m');
                let end_h = $(this).data('end_h');
                let end_m = $(this).data('end_m');
                let id = $(this).data('id');
                let staff_id = $(this).data('staff_id');
                let date = $(this).data('date');

                $('#shift-modal h1').text('時間変更');
                $('#shift-form input[name="staff_id"]').val(staff_id);
                $('#shift-form input[name="date"]').val(date);
                $('#shift-form select[name="start_h"]').val(start_h);
                $('#shift-form select[name="start_m"]').val(start_m);
                $('#shift-form select[name="end_h"]').val(end_h);
                $('#shift-form select[name="end_m"]').val(end_m);
                $('#shift-form input[name="id"]').val(id);
                $('#shift-form input[name="_method"]').val('PUT');
                $('#shift-form input[name="type"]').val('modify');
                $('#shift-modal #delete-bt').attr('data-shift_id', id);

                $('#shift-modal').remodal().open();
                return false;
            });

            /* -------------------------------------
             モーダル確定
            ------------------------------------- */
            $(document).on('click', '#shift-form .remodal-confirm', function () {
                let staff_id = $('#shift-form input[name="staff_id"]').val();
                let date = $('#shift-form input[name="date"]').val();
                let date_attr = '#work-' + date + '-' + staff_id;
                let start_h = $('#shift-form select[name="start_h"]').val();
                let start_m = $('#shift-form select[name="start_m"]').val();
                let end_h = $('#shift-form select[name="end_h"]').val();
                let end_m = $('#shift-form select[name="end_m"]').val();
                let token = $('input[name="_token"]').val();
                let type = $('#shift-form input[name="type"]').val();
                let shift_id = '';
                let _method = '';
                let url = '';
                if (type === 'modify') {
                    shift_id = $('#shift-form input[name="id"]').val();
                    url = '/shift/' + shift_id;
                    _method = $('#shift-form input[name="_method"]').val();
                }

                $('#shift-form').attr('action', url);

                // Ajax通信を開始
                $.ajax({
                    url: url,
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        'id': shift_id,
                        'staff_id': staff_id,
                        'date': date,
                        'start_h': start_h,
                        'start_m': start_m,
                        'end_h': end_h,
                        'end_m': end_m,
                        '_token': token,
                        '_method': _method
                    },
                    timeout: 5000,
                })
                    .done(function (data) {
                        if (data['id']) {
                            $(date_attr).html(
                                '<a href="" class="modify-bt" ' +
                                'data-staff_id="' + staff_id + '"\n' +
                                'data-date="' + date + '"\n' +
                                'data-id="' + data['id'] + '"\n' +
                                'data-start_h="' + start_h + '"\n' +
                                'data-start_m="' + start_m + '"\n' +
                                'data-end_h="' + end_h + '"\n' +
                                'data-end_m="' + end_m + '"\n' +
                                '_method="' + _method + '"\n' +
                                '>\n' +
                                start_h + ':' + start_m + '<br>～<br>' + end_h + ':' + end_m + '\n' +
                                '</a>\n'
                            );
                        } else {
                            if (data['errors']) {
                                let errors = data.errors;
                                for (key in errors) {
                                    let message = errors[key][0];
                                    $('.alert').html('<li>' + message + '</li>');
                                }
                                $('.alert').show();
                            }
                        }
                    })
                    .fail(function (data) {
                        $('body').html(data.responseText);
                    });
                return false;

            });

            /* -------------------------------------
             削除ボタン
            ------------------------------------- */
            $(document).on('click', '#delete-bt', function () {
                let date = $('#shift-form input[name="date"]').val();
                let staff_id = $('#shift-form input[name="staff_id"]').val();
                let token = $('input[name="_token"]').val();
                let id = $(this).attr('data-shift_id');
                let date_attr = '#work-' + date + '-' + staff_id;

                // Ajax通信を開始
                $.ajax({
                    url: '/shift/' + id,
                    type: 'POST',
                    dataType: 'json',
                    // フォーム要素の内容をハッシュ形式に変換
                    data: {
                        '_token': token,
                        '_method': 'DELETE'
                    },
                    timeout: 5000,
                })
                    .done(function (data) {
                        if (data['result']) {
                            $('#shift-modal').remodal().close();
                            $(date_attr).html('<a href="" class="register-bt"' +
                            'data-date="' + staff_id + '"' +
                            'data-staff_id="' + staff_id + '">' +
                            '登録' +
                            ' </a>');
                        } else {
                            alert('削除失敗しました');
                        }
                    })
                    .fail(function (data) {
                        $('body').html(data.responseText);
                    });
                return false;
            });

        });
    </script>
@endsection

@section('main-contents')
    <h1>シフト管理(PCのみ対応)</h1>

    <div class="alert alert-danger">
        <ul></ul>
    </div>

    <a href="/" class="btn btn-secondary btn-sm" id="top-link">
        トップに戻る
    </a>

    <article id="shift">

        <p id="month-title">{{$str_month}}</p>

        <div id="calender-nav">
            <nav class="btn-group" role="group">
                <a id="prev-month" href="/shift?month={{$prev_month}}" class="btn btn-warning btn-xs">前の月</a>
                <a id="this-month" href="/shift" class="btn btn-default btn-xs">今月</a>
                <a id="next-month" href="/shift?month={{$next_month}}" class="btn btn-success btn-xs">次の月</a>
            </nav>
        </div>

        <table class="table table-striped table-bordered">
            <tr>
                <th id="staff-register-link">
                    <a id="next-month" href="/staff/register" class="btn btn-info btn-xs">スタッフ登録</a>
                </th>
                @foreach($shift_list as $row)
                    <th class="{{$row->week_en}}">
                        {{$row->day}}<br>
                        <span>{{$row->week_jp}}</span>
                    </th>
                @endforeach
            </tr>

            @foreach($staff_data as $staff)
                <tr>
                    <th class="staff-name">{{$staff->name}}</th>

                    @foreach($shift_list as $shift)

                        {{--シフトが入っていない--}}
                        @if(empty($shift->data[$staff->id]))
                            <td class="time" id="work-{{$shift->date}}-{{$staff->id}}">
                                <a href="" class="register-bt"
                                   data-date="{{$shift->date}}"
                                   data-staff_id="{{$staff->id}}">
                                    登録
                                </a>
                            </td>
                        @endif

                        {{--シフトが入っている--}}
                        @if(!empty($shift->data[$staff->id][$shift->date]))
                            <td class="time" id="work-{{$shift->date}}-{{$staff->id}}">
                                <a href=""
                                   class="modify-bt"
                                   data-staff_id="{{$staff->id}}"
                                   data-date="{{$shift->data[$staff->id][$shift->date]->date}}"
                                   data-id="{{$shift->data[$staff->id][$shift->date]->id}}"
                                   data-start_h="{{$shift->data[$staff->id][$shift->date]->start_h}}"
                                   data-start_m="{{$shift->data[$staff->id][$shift->date]->start_m}}"
                                   data-end_h="{{$shift->data[$staff->id][$shift->date]->end_h}}"
                                   data-end_m="{{$shift->data[$staff->id][$shift->date]->end_m}}"
                                >
                                    {{$shift->data[$staff->id][$shift->date]->start_h}}:{{$shift->data[$staff->id][$shift->date]->start_m}}<br>
                                    〜<br>
                                    {{$shift->data[$staff->id][$shift->date]->end_h}}:{{$shift->data[$staff->id][$shift->date]->end_m}}
                                </a>
                            </td>
                        @endif

                    @endforeach
                </tr>
            @endforeach
        </table>
    </article>

    <!--== 出勤登録用モーダル ===================-->
    <section class="remodal" id="shift-modal" data-remodal-id="shift-modal"
             data-remodal-options="hashTracking:false">
        <h1>出勤登録</h1>

        <form action="" method="post" id="shift-form">
            <p id="title"></p>
            <ul id="register-box">
                <li>
                    <p class="time-title">出勤時間</p>
                    <div class="start-time">
                        <select class="form-control w100" name="start_h">
                            @foreach($hour_item as $val)
                                <option option="{{ $val }}">{{ $val }}</option>
                            @endforeach
                        </select>
                    </div>

                    <p class="time-separator">:</p>

                    <div class="start-min">
                        <select class="form-control w100" name="start_m">
                            @foreach($minute_item as $val)
                                <option option="{{ $val }}">{{ $val }}</option>
                            @endforeach
                        </select>
                    </div>
                </li>

                <li>
                    <p class="time-title">退勤時間</p>
                    <div class="end-time">
                        <select class="form-control w100" name="end_h">
                            @foreach($hour_item as $val)
                                <option option="{{ $val }}">{{ $val }}</option>
                            @endforeach
                        </select>
                    </div>

                    <p class="time-separator">:</p>

                    <div class="end-min">
                        <select class="form-control w100" name="end_m">
                            @foreach($minute_item as $val)
                                <option option="{{ $val }}">{{ $val }}</option>
                            @endforeach
                        </select>
                    </div>
                </li>
            </ul>

            @csrf
            <input type="hidden" name="date" value=""/>
            <input type="hidden" name="staff_id" value=""/>
            <input type="hidden" name="id" value=""/>
            <input type="hidden" name="type" value=""/>
            <input type="hidden" name="_method" value=""/>
            <a data-remodal-action="cancel" class="remodal-cancel">閉じる</a>
            <a data-remodal-action="confirm" class="remodal-confirm">登録する</a>
            <div id="delete-bt-box">
                <a href="" id="delete-bt" data-shift_id="">削除する</a>
            </div>
        </form>
    </section>
@endsection
