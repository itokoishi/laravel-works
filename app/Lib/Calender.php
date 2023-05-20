<?php

namespace App\Lib;

use App\Models\Schedule;
use DateTime;
use Illuminate\Database\Eloquent\Collection;
use stdClass;

class Calender
{
    use ItokoishiTrait;

    /**
     * 予約カレンダーを返す
     * @param string $month
     * @param string $last_day
     * @param string $today
     * @return array
     * @throws \Exception
     */
    public function getTable(string $month, string $last_day, string $today): array
    {
        $calendar = [];
        $j        = 0;

        for ($i = 1; $i <= (int)$last_day; $i++) {
            //日付を作成する
            $target_date = $month . '-' . sprintf('%02d', $i);
            $day         = new DateTime($target_date);
            $week_no     = $day->format('w');

            if ($i == 1) {
                /*-- 1日目の曜日までをループ ---------------------------*/
                for ($s = 1; $s <= $week_no; $s++) {
                    /*-- 前半に空文字をセット ---------------------------*/
                    $obj              = new stdClass();
                    $obj->day         = '';
                    $obj->data        = [];
                    $obj->today_flag  = false;
                    $obj->target_date = '';
                    $calendar[$j]     = $obj;
                    $j++;
                }
            }

            $obj = new stdClass();

            /*-- 前半に空文字をセット ---------------------------*/
            $obj->day         = sprintf('%02d', $i);
            $obj->week_no     = $week_no;
            $obj->today_flag  = ($target_date == $today);
            $obj->target_date = $target_date;
            $obj->data        = $this->_getScheduleData($target_date);

            $calendar[$j] = $obj;
            $j++;

            /*-- 月末日の場合 ---------------------------*/
            if ($i == $last_day) {

                /*-- 月末日から残りをループ ---------------------------*/
                for ($e = 1; $e <= 6 - $week_no; $e++) {

                    /*-- 前半に空文字をセット ---------------------------*/
                    $obj              = new stdClass();
                    $obj->day         = '';
                    $obj->data        = [];
                    $obj->today_flag  = false;
                    $obj->target_date = '';
                    $calendar[$j]     = $obj;
                    $j++;
                }
            }
        }

        return $calendar;
    }

    private function _getScheduleData($target_date): Collection|array
    {
        return Schedule::query()->where('date', $target_date)->get();
    }
}
