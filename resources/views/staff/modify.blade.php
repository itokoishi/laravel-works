@extends('layouts.front-main')

@section('page-css')
    <link rel="stylesheet" href="/css/staff.css">
    <link rel="stylesheet" href="/croppie/croppie.css">
@endsection

@section('page-js')
    <script src="/croppie/croppie.js"></script>
    <script src="/js/staff-photo.js"></script>
    <script>
        $(function () {
            /* -------------------------------------
             登録ボタン
            ------------------------------------- */
            $(document).on('click', '#register-modal-bt', function () {
                $('#register-modal').remodal().open();
                return false;
            })

            $(document).on('click', '#register-bt', function () {
                let param = []
                param['name'] = $('input[name="name"]').val();
                param['name_kana'] = $('input[name="name_kana"]').val();
                param['birth_year'] = $('select[name="birth_year"]').val();
                param['birth_month'] = $('select[name="birth_month"]').val();
                param['birth_date'] = $('select[name="birth_date"]').val();

                /* -- バリデーション ---------------------*/
                let validate = isValid(param);
                if (!validate['result']) {
                    let text = '';
                    validate['message'].forEach((valid) => {
                        text += '<li>' + valid + '</li>';
                    });
                    $('#error-message').html(text);
                    $('.alert').show();
                    return false;
                }

                $('#staff-register-form').submit();
                return false;
            });

            @if(empty($modify_data->image))
                /*-----------------------------------------------
                写真の選択処理
                -----------------------------------------------*/
                cropPhoto();
            @endif

            /*-----------------------------------------------
            登録画像削除
            -----------------------------------------------*/
            $(document).on('click', '#photo-delete-modal-bt', function () {
                $('#photo-delete-modal').remodal().open()
                return false;
            });

            $(document).on('click', '#photo-delete-bt', function () {
                let id = $(this).data('id');
                let token = $('input[name="_token"]').val();

                //jax通信を開始
                $.ajax({
                    url: '/staff/modify/delete-image',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        'id': id,
                        '_token': token
                    },
                    timeout: 5000,
                })
                    .done(function (data) {
                        $('#photo-box').html(htmlTag());
                        cropPhoto();
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
        <h1>スタッフ登録</h1>

        <p id="test"></p>

        <ul id="error-message" class="alert alert-danger">
        </ul>

        <section>
            <form action="/staff/modify/execute" id="staff-register-form" method="post" enctype="multipart/form-data">
                <table class="table table-stsriped table-bordered" id="event-register">
                    <!--= 写真登録 =======================-->
                    <tr>
                        <th>写真登録</th>

                        <td id="photo-box">
                            @if(empty($modify_data->image))
                                <section id="content-box" class="">
                                    <div id="face-photo-result"></div>

                                    <div id="crop-result">
                                        <img src=""><br>
                                        <a href="" id="crop-delete-bt" class="btn btn-danger btn-sm">
                                            削除する
                                        </a>
                                    </div>

                                    <div id="select-bt">
                                        <label for="staff-photo" class="photo-upload-btn btn btn-success btn-sm">
                                            <span>画像選択</span>
                                            <input type="file" name="photo" id="staff-photo"/>
                                        </label>
                                    </div>

                                    <div id="croppie-bt-box">
                                        <a href="" id="croppie-bt" class="btn btn-warning btn-sm">
                                            確定する
                                        </a>
                                    </div>

                                    <div class="js-cord">
                                        <input type="hidden" name="x1">
                                        <input type="hidden" name="y1">
                                        <input type="hidden" name="x2">
                                        <input type="hidden" name="y2">
                                    </div>
                                </section>
                            @else
                                <section id="content-box">
                                    <img src="/image/staff/{{$modify_data->id}}?{{$modify_data->updated_at}}" class="staff-image"/>
                                    <a href="" id="photo-delete-modal-bt"
                                       class="btn btn-danger btn-sm">
                                        削除する
                                    </a>
                                </section>
                            @endif
                        </td>
                    </tr>

                    <!--= 名前 =======================-->
                    <tr>
                        <th>名前<span class="note">必須</span></th>
                        <td>
                            <input name="name" value="{{old('name', $modify_data->name)}}" class="form-control w500">
                            @error('name')
                            <span class="note">{{ $message }}</span>
                            @enderror
                        </td>
                    </tr>

                    <!--= なまえ(かな) =======================-->
                    <tr>
                        <th>なまえ(かな)<span class="note">必須</span></th>
                        <td>
                            <input name="name_kana" value="{{old('name_kana', $modify_data->name_kana)}}"
                                   class="form-control w500">
                            @error('name_kana')
                            <span class="note">{{ $message }}</span>
                            @enderror
                        </td>
                    </tr>

                    <!--= 生年月日 =======================-->
                    <tr>
                        <th>生年月日<span class="note">必須</span></th>
                        <td>
                            <select name="birth_year" class="form-control w100 middle-line">
                                @foreach($year_items as $val)
                                    <option value="{{$val['val']}}"
                                        {{old('birth_year', $modify_data->birth_year) == $val['val'] ? 'selected=selected':'' }}>
                                        {{$val['val']}}
                                    </option>
                                @endforeach
                            </select>

                            <p class="middle-line">年</p>

                            <select name="birth_month" class="form-control w50 middle-line">
                                @foreach($month_items as $val)
                                    <option value="{{$val}}"
                                        {{old('birth_month', $modify_data->birth_month) == $val ? 'selected=selected':'' }}>
                                        {{$val}}
                                    </option>
                                @endforeach
                            </select>

                            <p class="middle-line">月</p>

                            <select name="birth_date" class="form-control w50 middle-line">
                                @foreach($date_items as $val)
                                    <option value="{{$val}}"
                                        {{old('birth_date', $modify_data->birth_date) == $val ? 'selected=selected':'' }}>
                                        {{$val}}
                                    </option>
                                @endforeach
                            </select>

                            <p class="middle-line">日</p>

                            @error('birth_year')
                            <span class="note">{{ $message }}</span>
                            @enderror

                            @error('birth_month')
                            <span class="note">{{ $message }}</span>
                            @enderror

                            @error('birth_date')
                            <span class="note">{{ $message }}</span>
                            @enderror
                        </td>
                    </tr>

                    <!--= 表示設定 =======================-->
                    <tr>
                        <th>表示設定</th>
                        <td>
                            <select class="form-control w150" name="view_flag">
                                <option value="0"
                                    {{old('view_flag', $modify_data->view_flag) == 0 ? 'selected=selected':''}}>
                                    表示しない
                                </option>
                                <option value="1"
                                    {{old('view_flag', $modify_data->view_flag) == 1 ? 'selected=selected':''}}>
                                    表示する
                                </option>
                            </select>
                        </td>
                    </tr>
                </table>
                @csrf
                <input type="hidden" name="id" value="{{$modify_data->id}}">
            </form>

            <div id="register-bt-box">
                <a href="" class="btn btn-info btn-sm" id="register-modal-bt">更新する</a>
            </div>
        </section>
    </article>

    <section class="remodal" id="register-modal" data-remodal-id="register-modal"
             data-remodal-options="hashTracking:false">
        <h1>スタッフ情報更新</h1>

        <p>スタッフ情報を更新しますか？</p>
        <a data-remodal-action="cancel" class="remodal-cancel">閉じる</a>
        <a data-remodal-action="cancel" class="remodal-confirm" id="register-bt">登録する</a>
    </section>

    <section class="remodal" id="photo-delete-modal" data-remodal-id="photo-delete-modal"
             data-remodal-options="hashTracking:false">
        <h1>スタッフ画像の削除</h1>

        <p>スタッフ画像を削除しますか？</p>
        <a data-remodal-action="cancel" class="remodal-cancel">閉じる</a>
        <a data-remodal-action="cancel"
           class="remodal-confirm"
           id="photo-delete-bt"
           data-id="{{$modify_data->id}}">
            削除する
        </a>
    </section>
@endsection
