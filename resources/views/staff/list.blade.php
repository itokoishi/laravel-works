@extends('layouts.front-main')

@section('page-css')
    <link rel="stylesheet" href="/css/staff.css">
@endsection

@section('page-js')
    <script>
        $(function(){
            $(document).on('click', '.delete-modal-bt', function () {
                let id = $(this).data('id');
                $('#delete-modal input[name="id"]').val(id);
                $('#delete-modal').remodal().open();
                return false;
            });

            $(document).on('click', '#delete-bt', function () {
                let token = $('input[name="_token"]').val();
                let id = $('#delete-modal input[name="id"]').val();

                //jax通信を開始
                $.ajax({
                    url: '/staff/list/delete',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        'id': id,
                        '_token': token
                    },
                    timeout: 5000,
                })
                    .done(function (data) {
                        $('.staff' + id).hide();
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
    <article>
        <h1>スタッフ一覧（上限５件まで）</h1>

        @if(session()->has('result'))
            <ul class="alert alert-{{ session('result')->tag }}">
                @foreach(session('result')->messages as $message)
                    <li>{{ $message }}</li>
                @endforeach
            </ul>
        @endif

        <div id="register-link-box">
            <a href="/staff/register" class="btn btn-success btn-sm">新規登録</a>
        </div>

        <section id="staff-list">
            @foreach($list as $row)
                <div class="staff-content{{$row->view_flag ? '' : ' not_view'}} staff{{$row->id}}" >
                    <div class="image">
                        <img src="/image/staff/{{$row->id}}?{{$row->updated_at}}"/>
                        {!! $row->view_flag ? '' : '<span class="note">非表示中</span>' !!}
                    </div>
                    <div class="name">
                        {{$row->name}}<br>
                        ({{$row->name_kana}})
                    </div>
                    <div class="birthday">
                        {{$row->birth_year}}年{{$row->birth_month}}月{{$row->birth_date}}日
                    </div>
                    <div class="bt-box">
                        <a href="/staff/modify/{{$row->id}}" class="btn btn-success btn-xs">編集する</a>
                        <a href="" class="btn btn-danger btn-xs delete-modal-bt" data-id="{{$row->id}}">削除する</a>
                    </div>
                </div>
            @endforeach
        </section>
    </article>

    <section class="remodal" id="delete-modal" data-remodal-id="delete-modal"
             data-remodal-options="hashTracking:false">
        <h1>スタッフ削除</h1>

        <p>スタッフを削除しますか？</p>
        <input type="hidden" name="id" value="">
        @csrf
        <a data-remodal-action="cancel" class="remodal-cancel">閉じる</a>
        <a data-remodal-action="cancel" class="remodal-confirm" id="delete-bt">削除する</a>
    </section>

@endsection
