<?php

namespace App\Http\Controllers;

use App\Lib\Calender;
use App\Models\Schedule;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CalenderController extends CommonController
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * カレンダー表示
     * @param Request $request
     * @return Application|Factory|View
     * @throws Exception
     */
    public function index(Request $request): View|Factory|Application
    {
        $month = $request->get('month', $this->_this_month);

        $calender  = new Calender();
        $month_obj = new \DateTime($month);

        /* -- 日付の整形 ---------------------*/
        $last_day  = $month_obj->modify('last day of this month')->format('d');
        $str_month  = $month_obj->format('Y年m月');
        $next_month = $month_obj->modify('next month ' . $month)->format('Y-m');
        $prev_month = $month_obj->modify('previous month ' . $month)->format('Y-m');

        /* -- 表示変数 ---------------------*/
        $this->_items['prev_month'] = $prev_month;
        $this->_items['next_month'] = $next_month;
        $this->_items['str_month']  = $str_month;
        $this->_items['calender']   = $calender->getTable($month, $last_day, $this->_today);
        return view('calender', $this->_items);
    }

    /**
     * 登録処理
     * @param Request $request
     * @return false|JsonResponse|string
     */
    public function store(Request $request): bool|JsonResponse|string
    {
        $validator = Validator::make(
            $request->all(),
            [
                'title' => 'required',
                'date' => 'required|date',
            ]
        );

        if ($validator->fails()) {
            return response()->json(
                ['errors' => $validator->getMessageBag()->toArray(),]);
        }

        $id = $this->_insertSchedule($request);
        return json_encode(['id'=>$id]);
    }

    /**
     * アップデート
     * @param Request $request
     * @param         $id
     * @return false|string
     */
    public function update(Request $request, $id): bool|string
    {
        $this->_updateSchedule($request, $id);

        return json_encode(['id'=>$id]);
    }


    /**
     * 削除
     * @param $id
     * @return bool|string
     */
    public function destroy($id): bool|string
    {
        Schedule::query()->where('id', $id)->delete();
        return json_encode(['id'=>$id]);
    }

    /**
     * DBアップデート
     * @param $request
     * @param $id
     * @return void
     */
    private function _updateSchedule($request, $id)
    {
        $schedule = Schedule::query()->find($id);
        $schedule->title = $request->post('title', '');
        $schedule->memo = $request->post('memo', '');
        $schedule->date = $request->post('date', '');
        $schedule->save();
    }

    /**
     * DBインサート
     * @param $request
     * @return mixed
     */
    private function _insertSchedule($request)
    {
        $schedule = new Schedule();
        $schedule->title = $request->post('title', '');
        $schedule->memo = $request->post('memo', '');
        $schedule->date = $request->post('date', '');
        $schedule->save();

        return $schedule->id;
    }

}
