<?php
namespace App\Http\Controllers;

use App\Lib\ItokoishiTrait;
use App\Models\Staff;

class ImageController extends CommonController
{

    use ItokoishiTrait;

    public function __construct()
    {
        parent::__construct();
    }

    public function staff($param)
    {
        /* -- 保存済み画像の取得 ---------------------*/
        $staff = Staff::query()->find($param);

        if(!empty($staff->image)){
            $file = $this->_storage_path . 'staff/' . $staff->image;

            if(!file_exists($file)){
                $file = $this->_storage_path . 'staff/default.jpg';
            }
        }else{
            $file = $this->_storage_path . 'staff/default.jpg';
        }

        return response()->file($file);
    }

}
