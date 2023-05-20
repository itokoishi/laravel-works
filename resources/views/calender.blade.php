@extends('layouts.front-main')

@section('page-css')
    <link rel="stylesheet" href="/css/calender.css?2022031801">
@endsection

@section('page-js')
    <script>
        $(function (){
            $('input[name="date"]').datepicker({
                format: 'yyyy-mm-dd',
                language: 'ja'
            });

            /*-----------------------------------------------
            登録処理
            -----------------------------------------------*/
            $(document).on('click', '#smart-register-bt', function(){
                $('input[name="title"]').val('');
                $('input[name="date"]').val('');
                $('textarea[name="memo"]').val('');
                $('input[name="_method"]').val('');
                $('input[name="id"]').val('');
                $('input[name="before_date"]').val('');
                $('#modify-bt').attr('id', 'register-bt');

                $('#delete-bt').hide();
                $('#register-bt').text('登録する');
                $('#register-modal h1').text('スケジュール登録');
                $('#register-modal').remodal().open();
                return false;
            });

            $(document).on('click', '#register-bt', function(){
                let title = $('input[name="title"]').val();
                let date = $('input[name="date"]').val();
                let memo = $('textarea[name="memo"]').val();
                let token = $('input[name="_token"]').val();

                //jax通信を開始
                $.ajax({
                    url: '/calender',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        'title': title,
                        'date': date,
                        'memo': memo,
                        '_token': token
                    },
                    timeout: 5000,
                })
                    .done(function (data) {
                        if(data['id']){
                            $('#date' + date).append(`
                                <a href=""
                                   class="schedule-text schedule` + date  + '_' + data['id'] + `"
                                   data-id="`+ data['id'] + `"
                                   data-memo="`+ memo + `"
                                   data-date="`+ date + `"
                                   data-title="`+ title + `">
                                    ` + title + `
                            </a>`);
                        }else{
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

            /*-----------------------------------------------
            更新処理
            -----------------------------------------------*/
            $(document).on('click', '.schedule-text', function(){
                let id = $(this).data('id');
                let title = $(this).data('title');
                let date = $(this).data('date');
                let memo = $(this).data('memo');
                let beforeDate = $(this).data('date');

                $('input[name="id"]').val(id);
                $('input[name="title"]').val(title);
                $('input[name="date"]').val(date);
                $('textarea[name="memo"]').val(memo);
                $('input[name="before_date"]').val(beforeDate);

                $('#delete-bt').show();
                $('#register-bt').text('更新する');
                $('#register-modal h1').text('スケジュール更新');
                $('#register-bt').attr('id', 'modify-bt');

                $('#register-modal').remodal().open();
                return false;
            });

            $(document).on('click', '#modify-bt', function() {
                let id = $('input[name="id"]').val();
                let title = $('input[name="title"]').val();
                let date = $('input[name="date"]').val();
                let memo = $('textarea[name="memo"]').val();
                let method = 'PUT';
                let token = $('input[name="_token"]').val();
                let beforeDate = $('input[name="before_date"]').val();

                //jax通信を開始
                $.ajax({
                    url: '/calender/' + id,
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        'title': title,
                        'date': date,
                        'memo': memo,
                        '_method': method,
                        '_token': token
                    },
                    timeout: 5000,
                })
                    .done(function (data) {
                        if (data['id']) {
                            $('.schedule' + beforeDate + '_' + id).hide();
                            $('#date' + date).append(`
                                <a href=""
                                   class="schedule-text schedule` + date  + '_' + data['id'] + `"
                                   data-id="`+ data['id'] + `"
                                   data-memo="`+ memo + `"
                                   data-date="`+ date + `"
                                   data-title="`+ title + `">
                                    ` + title + `
                            </a>`);
                        } else {
                            if (data['errors']) {
                                let errors = data.errors;
                                for (let key in errors) {
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

            $(document).on('click', '#delete-bt', function() {
                let id = $('input[name="id"]').val();
                let token = $('input[name="_token"]').val();
                let beforeDate = $('input[name="before_date"]').val();

                //jax通信を開始
                $.ajax({
                    url: '/calender/' + id,
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        '_method': 'DELETE',
                        '_token': token
                    },
                    timeout: 5000,
                })
                    .done(function (data) {
                        if (data['id']) {
                            $('.schedule' + beforeDate + '_' + id).hide();
                        } else {
                            if (data['errors']) {
                                let errors = data.errors;
                                for (let key in errors) {
                                    let message = errors[key][0];
                                    $('.alert').html('<li>' + message + '</li>');
                                }
                                $('.alert').show();
                            }
                        }
                        $('#register-modal').remodal().close();
                    })
                    .fail(function (data) {
                        $('body').html(data.responseText);
                        $('#register-modal').remodal().close();
                    });
                return false;
            });
        });
    </script>
@endsection

@section('main-contents')
    <article>
        <h1>カレンダー</h1>

        <div class="alert alert-danger">
            <ul></ul>
        </div>

        <section id="calender-content">

            <p id="month-title">{{$str_month}}</p>

            <!-- カレンダーコンテンツ -->
            <div id="calender-nav">
                <nav class="btn-group" role="group">
                    <a id="prev-month" href="/calender?month={{$prev_month}}" class="btn btn-warning btn-xs"> 前の月</a>
                    <a id="this-month" href="/calender" class="btn btn-default btn-xs">今月</a>
                    <a id="next-month" href="/calender?month={{$next_month}}" class="btn btn-success btn-xs"> 次の月</a>
                </nav>
            </div>

            <table class="calendar-table calender">
                <tr>
                    <th class="sun">日</th>
                    <th>月</th>
                    <th>火</th>
                    <th>水</th>
                    <th>木</th>
                    <th>金</th>
                    <th class="sat">土</th>
                </tr>

                @foreach($calender as $row)
                    @if($loop->index === 0)
                        <tr>
                    @endif

                        <td id="date{{$row->target_date}}"{{$row->today_flag == 1 ? ' class=today' : ''}}>
                            <span class="c-date">{{ $row->day }}</span><br>
                            @foreach($row->data as $data_row)
                                <a href=""
                                   class="schedule-text schedule{{$data_row->date}}_{{$data_row->id}}"
                                   data-id="{{$data_row->id}}"
                                   data-memo="{{$data_row->memo}}"
                                   data-date="{{$data_row->date}}"
                                   data-title="{{$data_row->title}}">
                                    {{$data_row->title}}
                                </a>
                            @endforeach
                        </td>

                    @if(($loop->index + 1) % 7 === 0 && $loop->index !== 0)
                        </tr>
                        <tr>
                    @endif
                @endforeach
            </table>
        </section>
    </article>


    <!--== スケジュール登録モーダル ===================-->
    <section class="remodal" id="register-modal" data-remodal-id="register-modal"
             data-remodal-options="hashTracking:false">
        <h1>スケジュール登録</h1>

        <form action="" method="post" id="schedule-form">
            <p id="title"></p>
            <ul id="register-box">
                <li>
                    <p class="form-title">タイトル</p>
                    <div>
                        <input type="text" name="title" value="" class="form-control w500 "/>
                    </div>
                </li>

                <li>
                    <p class="form-title">日付選択</p>
                    <div>
                        <input type="text" name="date" value="" class="form-control w150" />
                    </div>
                </li>

                <li>
                    <p class="form-title">メモ</p>
                    <div>
                        <textarea name="memo" class="form-control textarea1"></textarea>
                    </div>
                </li>
            </ul>

            <input type="hidden" name="before_date" value=""/>
            <input type="hidden" name="id" value=""/>
            <input type="hidden" name="_method" value=""/>
            @csrf
            <div id="calender-register-bt-box">
                <a data-remodal-action="cancel" class="remodal-cancel">閉じる</a>
                <a data-remodal-action="confirm" class="remodal-confirm" id="register-bt">登録する</a>
            </div>
            <a href="" class="text-danger" id="delete-bt">削除する</a>
        </form>
    </section>
@endsection
