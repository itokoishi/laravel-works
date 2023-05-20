<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shift extends Model
{
    use HasFactory;

    protected $table = 'shifts';

    protected $primaryKey = 'id';
    protected $dateFormat = 'Y-m-d H:i:s';

    /**
     * 登録処理(IDを返す)
     * @param $request
     * @return int
     */
    public static function insertShiftGetId($request): int
    {
        $shift           = new Shift();
        $shift->staff_id = $request->post('staff_id');
        $shift->date     = $request->post('date');
        $shift->start_h  = $request->post('start_h');
        $shift->start_m  = $request->post('start_m');
        $shift->end_h    = $request->post('end_h');
        $shift->end_m    = $request->post('end_m');
        $shift->save();
        return $shift->id;
    }

    /**
     * 登録処理(IDを返す)
     * @param $request
     * @param $id
     */
    public static function updateShift($request, $id)
    {
        $shift          = Shift::find($id);
        $shift->date    = $request->post('date');
        $shift->start_h = $request->post('start_h');
        $shift->start_m = $request->post('start_m');
        $shift->end_h   = $request->post('end_h');
        $shift->end_m   = $request->post('end_m');
        $shift->save();
    }
}
