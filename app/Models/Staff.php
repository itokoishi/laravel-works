<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\HigherOrderBuilderProxy;
use Illuminate\Database\Eloquent\Model;

class Staff extends Model
{
    use HasFactory;

    protected $table = 'staffs';

    protected $primaryKey = 'id';
    protected $dateFormat = 'Y-m-d H:i:s';

    /**
     * スタッフリスト取得
     * @return Builder[]|Collection
     */
    public static function getShiftList(): Collection|array
    {
        return self::query()
            ->where('view_flag', 1)
            ->orderBy('created_at', 'DESC')
            ->get();
    }

    /**
     * スタッフリスト取得
     * @return Builder[]|Collection
     */
    public static function getListAll(): Collection|array
    {
        return self::query()
            ->orderBy('view_flag', 'DESC')
            ->orderBy('created_at', 'DESC')
            ->get();
    }

    /**
     * 画像名の取得
     * @param $id
     * @return HigherOrderBuilderProxy|mixed
     */
    public static function getImage($id): mixed
    {
        $result = self::query()
            ->select('image')
            ->where('id', $id)
            ->first();

        return $result->image;
    }

    public static function updateStaff($request, $image_name)
    {
        $staff              = Staff::query()->find($request->post('id', ''));
        $staff->name        = $request->post('name', '');
        $staff->name_kana   = $request->post('name_kana', '');
        if (!empty($image_name)){
            $staff->image   = $image_name;
        }
        $staff->birth_year  = $request->post('birth_year', '');
        $staff->birth_month = $request->post('birth_month', '');
        $staff->birth_date  = $request->post('birth_date', '');
        $staff->view_flag   = $request->post('view_flag', '');
        $staff->save();
    }

    /**
     * 画像の削除
     * @param $id
     * @return void
     */
    public static function deleteImage($id)
    {
        $staff        = self::find($id);
        $staff->image = '';
        $staff->save();
    }
}
