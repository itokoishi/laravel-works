<?php

namespace App\Http\Controllers\Staff;

use App\Lib\ItokoishiTrait;
use App\Models\Staff;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use App\Http\Controllers\CommonController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class ModifyController extends CommonController
{

    use ItokoishiTrait;

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 更新ページ
     * @param $id
     * @return Application|Factory|View
     */
    public function index($id): View|Factory|Application
    {
        $modify_data = Staff::query()->where('id', $id)->first();

        $this->_items['modify_data'] = $modify_data;
        $this->_items['year_items'] = $this->_getYearArray();
        $this->_items['month_items'] = $this->_getMonthArray();
        $this->_items['date_items'] = $this->_getDateArray();
        return view('staff.modify', $this->_items);
    }

    /**
     * @throws ValidationException
     */
    public function execute(Request $request)
    {
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
            Staff::updateStaff($request, $file_name);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return abort(500);
        }

        /* -- パスワード処理 ---------------------*/
        $result = $this->_getResultMessage('success', ['スタッフの更新が完了しました']);
        $request->session()->flash('result', $result);
        return redirect('/staff/list');
    }

    /**
     * 画像削除処理
     * @param Request $request
     * @return array|string|null
     */
    public function deleteImage(Request $request): array|string|null
    {
        $id = $request->post('id', '');

        if(!empty($id)){
            $image_name = Staff::getImage($id);
            $staff_image = $this->_storage_path . 'staff/' . $image_name;
            if(file_exists($staff_image)){
                unlink($staff_image);
            }
            Staff::deleteImage($id);
        }
        return $id;
    }
}
