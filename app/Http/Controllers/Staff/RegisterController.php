<?php

namespace App\Http\Controllers\Staff;


use App\Http\Controllers\CommonController;
use App\Lib\ItokoishiTrait;
use App\Models\Staff;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class RegisterController extends CommonController
{

    use ItokoishiTrait;

    public function __construct()
    {
        parent::__construct();
    }

    public function index(): Factory|View|Application
    {

        $staff_count = Staff::query()->count();
        $is_staff_limit = $staff_count >= 5;

        $this->_items['year_items']  = $this->_getYearArray();
        $this->_items['month_items'] = $this->_getMonthArray();
        $this->_items['date_items']  = $this->_getDateArray();
        $this->_items['is_staff_limit']  = $is_staff_limit;
        return view('staff.register', $this->_items);
    }

    /**
     * 登録処理
     * @param Request $request
     * @return Application|RedirectResponse|Redirector|never|void
     * @throws ValidationException
     */
    public function store(Request $request)
    {
        $staff_count = Staff::query()->count();
        $is_staff_limit = $staff_count >= 5;

        if ($is_staff_limit){
            die('この登録は不正かつ上限数に達しています。');
        }

        $this->validate($request, [
            'name' => 'required',
            'name_kana' => 'required',
            'photo' => 'mimetypes:image/jpeg',
        ]);

        $photo     = $request->file('photo');
        $file_name = '';

        /*-----------------------------------------------
        ファイルの作成
        -----------------------------------------------*/
        if (isset($photo)) {
            $file_name     = sha1($this->_now_time) . '.jpg';
            $photo         = $request->file('photo');
            $square_width  = 200;
            $square_height = 200;

            $image        = imagecreatefromjpeg($photo->getPathname());
            $square_image = ImageCreateTrueColor($square_width, $square_width);

            $left_top_y    = (int)$request->post('y1');
            $left_top_x    = (int)$request->post('x1');
            $right_under_y = (int)$request->post('y2');
            $right_under_x = (int)$request->post('x2');
            $src_width     = $right_under_y - $left_top_y;
            $src_height    = $right_under_x - $left_top_x;

            imagecopyresampled(
                $square_image,
                $image,
                0,
                0,
                $left_top_x,
                $left_top_y,
                $square_width, $square_height,
                $src_width, $src_height,
            );

            imagejpeg($square_image, $this->_storage_path . 'staff/' . $file_name, 100);
        }

        try {
            $this->_insertStaff($request, $file_name);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return abort(500);
        }

        /* -- パスワード処理 ---------------------*/
        $result = $this->_getResultMessage('success', ['スタッフの登録が完了しました']);
        $request->session()->flash('result', $result);
        return redirect('/staff/list');
    }

    /**
     * データベース登録
     * @param $request
     * @param $file_name
     * @return void
     */
    public function _insertStaff($request, $file_name)
    {
        $staff              = new Staff();
        $staff->name        = $request->post('name', '');
        $staff->name_kana   = $request->post('name_kana', '');
        $staff->image       = $file_name;
        $staff->birth_year  = $request->post('birth_year', '');
        $staff->birth_month = $request->post('birth_month', '');
        $staff->birth_date  = $request->post('birth_date', '');
        $staff->view_flag   = $request->post('view_flag', 0);

        $staff->save();
    }
}
