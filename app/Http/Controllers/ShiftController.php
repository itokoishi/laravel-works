<?php

namespace App\Http\Controllers;

use App\Lib\ItokoishiTrait;
use App\Models\Schedule;
use App\Models\Shift;
use App\Models\Staff;
use DateTime;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use stdClass;

class ShiftController extends CommonController
{
    use ItokoishiTrait;

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @param Request $request
     * @return Application|Factory|View
     * @throws Exception
     */
    public function index(Request $request): View|Factory|Application
    {
        /*-----------------------------------------------
        日付の情報を取得
        -----------------------------------------------*/
        $target_month = $request->get('month', $this->_this_month);
        $month_obj        = new DateTime($target_month);
        $last_day     = $month_obj->modify('last day of this month')->format('d');
        $shift_list   = $this->_getDateList($last_day, $target_month);

        /* -- 日付の整形 ---------------------*/
        $str_month  = $month_obj->format('Y年m月');
        $next_month = $month_obj->modify('next month ' . $target_month)->format('Y-m');
        $prev_month = $month_obj->modify('previous month ' . $target_month)->format('Y-m');

        /*-----------------------------------------------
        スタッフ情報を取得
        -----------------------------------------------*/
        $staff_data = Staff::getShiftList();

        /*-----------------------------------------------
        表示用変数
        -----------------------------------------------*/
        $this->_items['str_month']  = $str_month;
        $this->_items['prev_month'] = $prev_month;
        $this->_items['next_month'] = $next_month;
        $this->_items['shift_list']  = $shift_list;
        $this->_items['staff_data']  = $staff_data;
        $this->_items['hour_item']   = $this->_getHourArray();
        $this->_items['minute_item'] = $this->_getMinuteArray();
        return view('shift', $this->_items);
    }

    /**
     * 登録処理
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        /*-----------------------------------------------
        バリデーション
        -----------------------------------------------*/
        $validator = Validator::make(
            $request->all(),
            [
                'staff_id' => 'required|alpha_num',
                'date'     => 'required|date',
                'start_h'  => 'required|alpha_num',
                'start_m'  => 'required|alpha_num',
                'end_h'    => 'required|gt:start_h|alpha_num',
                'end_m'    => 'required|alpha_num',
            ],
            [
                'end_h.gt' => '退勤時間は出勤時間より遅くなければいけません',
            ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    'errors' => $validator->getMessageBag()->toArray(),
                ]);
        }

        /*-----------------------------------------------
        登録処理
        -----------------------------------------------*/
        try {
            $latest_id = Shift::insertShiftGetId($request);
        }catch (Exception $e){
            Log::error($e);
        }
        return response()->json(['id' => $latest_id]);

    }

    /**
     * アップデート処理
     * @param Request $request
     * @param         $id
     * @return JsonResponse
     */
    public function update(Request $request, $id): JsonResponse
    {
        /*-----------------------------------------------
        バリデーション
        -----------------------------------------------*/
        $validator = Validator::make(
            $request->all(),
            [
                'staff_id' => 'required|alpha_num',
                'date'     => 'required|date',
                'start_h'  => 'required|alpha_num',
                'start_m'  => 'required|alpha_num',
                'end_h'    => 'required|gt:start_h|alpha_num',
                'end_m'    => 'required|alpha_num',
            ],
            [
                'end_h.gt' => '退勤時間は出勤時間より遅くなければいけません',
            ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    'errors' => $validator->getMessageBag()->toArray(),
                ]);
        }

        Shift::updateShift($request, $id);
        return response()->json(['id' => $id]);
    }

    public function destroy(Request $request, $id)
    {
        try {
            Shift::query()->where('id', $id)->delete();
        }catch (Exception $e){
            Log::error($e);
            return abort(500);
        }

        return response()->json(['result' => true]);
    }

    /**
     * シフトの表示データ
     * @param $shift_data
     * @return array
     */
    private function _getShiftViewData($shift_data): array
    {
        $shift_list = [];

        foreach ($shift_data as $row) {
            $shift_list[$row->staff_id][$row->date] = $row;
        }

        return $shift_list;
    }

    /**
     * 日にちのリスト
     * @param $last_day
     * @param $month
     * @return array
     * @throws Exception
     */
    private function _getDateList($last_day, $month): array
    {
        $day_list = [];

        for ($i = 1; $i <= $last_day; $i++) {
            $day       = sprintf('%02d', $i);
            $date      = new DateTime($month . '-' . $day);
            $week_no   = $date->format('w');
            $week_list = $this->_getWeekList();
            $week_jp   = $week_list[$week_no];

            /* -- シフトデータ ---------------------*/
            $shift_data = Shift::query()->where('date', $date)->get();
            $shift_data = $this->_getShiftViewData($shift_data);

            $day_list[$i]          = new stdClass();
            $day_list[$i]->day     = $day;
            $day_list[$i]->date    = $month . '-' . $day;
            $day_list[$i]->week_jp = $week_jp;
            $day_list[$i]->data    = $shift_data;
            if ($week_jp === '土') {
                $day_list[$i]->week_en = 'sat';
            } elseif ($week_jp === '日') {
                $day_list[$i]->week_en = 'sun';
            } else {
                $day_list[$i]->week_en = '';
            }
        }

        return $day_list;
    }
}
