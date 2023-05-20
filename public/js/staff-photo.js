/**
 * バリデーション
 * @param param
 * @returns {*[]}
 */
const isValid = (param) => {
    let validate = [];
    validate['result'] = true;
    validate['message'] = [];

    if (!param['name']) {
        validate['result'] = false;
        validate['message'][0] = '名前は必須です。';
    }

    if (!param['name_kana']) {
        validate['result'] = false;
        validate['name_kana'] = [];
        validate['message'][1] = 'なまえ(かな)は必須です。';
    }

    if (!param['birth_year'] || !param['birth_month'] || !param['birth_date']) {
        validate['result'] = false;
        validate['birth_day'] = [];
        validate['message'][2] = '生年月日は必須です。';
    }

    return validate;
}

const htmlTag = () => {

    return `
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
    </section>`;
}

/**
 * クロップ処理
 */
const cropPhoto = () => {

    /*-----------------------------------------------
    クロップ初期化
    -----------------------------------------------*/
    let uploadCrop = $('#face-photo-result').croppie({
        viewport: {width: 200, height: 200, type: 'circle'},
        boundary: {width: 250, height: 250},
        url: '/image/staff/default'
    });

    uploadCrop.on('update.croppie', function (e, cropData) {
        $('input[name="x1"]').val(cropData.points[0]);
        $('input[name="y1"]').val(cropData.points[1]);
        $('input[name="x2"]').val(cropData.points[2]);
        $('input[name="y2"]').val(cropData.points[3]);
    });

    /*-----------------------------------------------
    写真選択
    -----------------------------------------------*/
    $(document).on('change', '#staff-photo', function () {

        /* -- ファイルの読み込み ---------------------*/
        if (this.files && this.files[0]) {

            /* -- ファイルタイプチェック ---------------------*/
            if (this.files[0].type !== 'image/jpeg') {
                let validate = [];
                validate['result'] = false;
                validate['message'] = [];
                validate['message'][0] = '画像はjpegのみとなります。';

                let text = '';
                validate['message'].forEach((valid) => {
                    text += '<li>' + valid + '</li>';
                });
                $('#error-message').html(text);
                $('.alert').show();
                return false;
            }

            let reader = new FileReader();

            reader.onload = function (e) {

                uploadCrop.croppie('bind', {
                    url: e.target.result
                });

                $('#face-photo-result').addClass('ready');
            }

            $('#select-bt').hide();
            $('#croppie-bt-box').show();
            reader.readAsDataURL(this.files[0]);
        }
    });

    /*-----------------------------------------------
    画像クロップ
    -----------------------------------------------*/
    $(document).on('click', '#croppie-bt', function () {
        uploadCrop.croppie('result', 'base64').then(function (base64) {
            $('#face-photo-result').hide();
            $('#crop-result img').attr('src', base64);
            $('#select-bt').hide();
            $('#croppie-bt-box').hide();
            $('#crop-result').show();
        });
        return false;
    });

    /*-----------------------------------------------
    クロップ画像削除
    -----------------------------------------------*/
    $(document).on('click', '#crop-delete-bt', function () {
        $('input[name="photo"]').val(null);
        uploadCrop = $('#face-photo-result').croppie({
            viewport: {width: 200, height: 200, type: 'circle'},
            boundary: {width: 250, height: 250},
            url: '/image/staff/default'
        });
        $('#face-photo-result').show();
        $('#select-bt').show();
        $('#crop-result').hide();
        return false;
    });
}
