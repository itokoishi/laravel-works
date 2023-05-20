@extends('layouts.front-main')

@section('page-css')
    <link rel="stylesheet" href="/css/staff.css">
    <link rel="stylesheet" href="/croppie/croppie.css">
@endsection

@section('page-js')
    <script src="/croppie/croppie.js"></script>
    <script>
        $(function () {
            let uploadCrop;

            function readFile(input) {

                if (input.files && input.files[0]) {
                    let reader = new FileReader();

                    reader.onload = function (e) {
                        uploadCrop.croppie('bind', {
                            url: e.target.result
                        });
                        $('#face-photo-result').addClass('ready');
                    }

                    reader.readAsDataURL(input.files[0]);
                }
            }

            uploadCrop = $('#face-photo-result').croppie({
                viewport: {width: 350, height: 350, type: 'circle'},
                boundary: {width: 350, height: 350},
                url: '/image/staff/default'
            });

            uploadCrop.on('update.croppie', function (e, cropData) {
                $('input[name="x1"]').val(cropData.points[0]);
                $('input[name="y1"]').val(cropData.points[1]);
                $('input[name="x2"]').val(cropData.points[2]);
                $('input[name="y2"]').val(cropData.points[3]);
            });

            $('#staff-photo').change(function () {
                readFile(this);
            });

            $('#register-photo-bt').on('click', function (ev) {
                uploadCrop.croppie('result', {
                    type: 'canvas',
                    size: 'original'
                }).then(function (resp, cropData) {
                    $('#image_base64').val(resp);
                    $('#photo-form').submit();
                });
                return false;
            });
        });
    </script>
@endsection

@section('main-contents')
    <section id="photo-content">
        <!--================== ナビゲーション ==================-->
        <h1>写真登録</h1>

        <form action="" method="post" id="photo-form" enctype="multipart/form-data">
            <section id="content-box" class="">
                <div id="face-photo-result"></div>

                <div id="select-bt">
                    <label for="staff-photo" class="photo-upload-btn btn btn-success btn-sm">
                        <span>画像選択</span>
                        <input type="file" name="photo" id="staff-photo"/>
                    </label>
                </div>

                <div class="js-coord">
                    <input type="hidden" name="x1">
                    <input type="hidden" name="y1">
                    <input type="hidden" name="x2">
                    <input type="hidden" name="y2">
                </div>
            </section>

            <input type="hidden" name="therapist_id" value="" />
            <input type="hidden" name="type" value="" />
            @csrf
        </form>

        <div id="register-photo">
            <a href="" id="register-photo-bt" class="btn btn-info btn-sm">画像を登録する</a><br>
            <a href="/admin/staff/register" class="return-bt note">戻る</a>
        </div>
    </section>
@endsection
