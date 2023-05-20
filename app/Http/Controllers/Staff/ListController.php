<?php

namespace App\Http\Controllers\Staff;


use App\Http\Controllers\CommonController;
use App\Lib\ItokoishiTrait;
use App\Models\Staff;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class ListController extends CommonController
{

    use ItokoishiTrait;

    public function __construct()
    {
        parent::__construct();
    }

    public function index(): Factory|View|Application
    {
        $list = Staff::getListAll();

        $this->_items['list'] = $list;
        return view('staff.list', $this->_items);
    }

    /**
     * 更新情報の削除
     * @param Request $request
     * @return bool|string
     */
    public function delete(Request $request): bool|string
    {
        $id = $request->post('id', '');

        if(!empty($id)){
            $image_name = Staff::getImage($id);

            if(!empty($image_name)){
                $staff_image = $this->_storage_path . 'staff/' . $image_name;
                if(file_exists($staff_image)){
                    unlink($staff_image);
                }
                Staff::deleteImage($id);
            }
        }

        Staff::query()->where('id', $id)->delete();
        return json_encode(['id' => $id]);
    }
}
