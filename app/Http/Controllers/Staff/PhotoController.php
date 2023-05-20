<?php
namespace App\Http\Controllers\Staff;


use App\Http\Controllers\CommonController;
use App\Lib\ItokoishiTrait;
use Illuminate\Http\Request;

class PhotoController extends CommonController
{

    use ItokoishiTrait;

    public function __construct()
    {
        parent::__construct();
    }

    public function index(Request $request)
    {

        $this->_items['type'] = $request->get('type', '');
        $this->_items['therapist_id'] = $request->get('staff_id', '');
        return view('staff.photo', $this->_items);
    }
}
