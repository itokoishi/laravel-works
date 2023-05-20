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
                    validate['message'].forEach((valid) =>{ text += '<li>' + valid + '</li>'; });
                    $('#error-message').html(text);
                    $('.alert').show();
                    return false;
                }

                $('#staff-register-form').submit();
                return false;
            });

            /*-----------------------------------------------
            写真の選択処理
            -----------------------------------------------*/
            cropPhoto();

        });
    </script>
@endsection

@section('main-contents')
    <article>
        <h1>スタッフ登録</h1>

        @if($is_staff_limit)
            <p class="alert alert-danger" style="display: block;">５件が上限のため、これ以上登録はできません</p>
        @else
        <ul id="error-message" class="alert alert-danger">
        </ul>

        <section>
            <form id="staff-register-form" method="post" enctype="multipart/form-data">
                <table class="table table-striped table-bordered" id="event-register">
                    <!--= 写真登録 =======================-->
                    <tr>
                        <th>写真登録</th>

                        <td id="photo-box">
                            <p class="phone-title">写真登録</p>
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
                        </td>
                    </tr>

                    <!--= 名前 =======================-->
                    <tr>
                        <th>名前<span class="note">必須</span></th>
                        <td>
                            <p class="phone-title">名前<span class="note">(必須)</span></p>
                            <input name="name" value="" class="form-control w500">
                            @error('name')
                            <span class="note">{{ $message }}</span>
                            @enderror
                        </td>
                    </tr>

                    <!--= なまえ(かな) =======================-->
                    <tr>
                        <th>なまえ(かな)<span class="note">必須</span></th>
                        <td>
                            <p class="phone-title">なまえ(かな)<span class="note">(必須)</span></p>
                            <input name="name_kana" value="" class="form-control w500">
                            @error('name_kana')
                            <span class="note">{{ $message }}</span>
                            @enderror
                        </td>
                    </tr>

                    <!--= 生年月日 =======================-->
                    <tr>
                        <th>生年月日<span class="note">必須</span></th>
                        <td>
                            <p class="phone-title">生年月日<span class="note">(必須)</span></p>
                            <select name="birth_year" class="form-control w100 middle-line">
                                @foreach($year_items as $val)
                                    <option
                                        value="{{$val['val']}}"
                                        {{old('year', $val['initialize']) == $val['val'] ? 'selected=selected':'' }}>
                                        {{$val['val']}}
                                    </option>
                                @endforeach
                            </select>

                            <p class="middle-line">年</p>

                            <select name="birth_month" class="form-control w50 middle-line">
                                @foreach($month_items as $val)
                                    <option value="{{$val}}">{{$val}}</option>
                                @endforeach
                            </select>

                            <p class="middle-line">月</p>

                            <select name="birth_date" class="form-control w50 middle-line">
                                @foreach($date_items as $val)
                                    <option value="{{$val}}">{{$val}}</option>
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
                            <p class="phone-title">表示設定</p>
                            <select class="form-control w150" name="view_flag">
                                <option value="0">表示しない</option>
                                <option value="1">表示する</option>
                            </select>
                        </td>
                    </tr>
                </table>
                @csrf
            </form>

            <div id="register-bt-box">
                <a href="" class="btn btn-info btn-sm" id="register-modal-bt">登録する</a>
            </div>
        </section>
        @endif
    </article>

    <section class="remodal" id="register-modal" data-remodal-id="register-modal"
             data-remodal-options="hashTracking:false">
        <h1>スタッフ登録</h1>

        <p>スタッフを登録しますか？</p>
        <a data-remodal-action="cancel" class="remodal-cancel">閉じる</a>
        <a data-remodal-action="cancel" class="remodal-confirm" id="register-bt">登録する</a>
    </section>
@endsection
