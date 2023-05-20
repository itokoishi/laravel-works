<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;

class LoginController extends CommonController
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * ログインページ
     * @return Application|Factory|View
     */
    public function index(): View|Factory|Application
    {
        return view('login', $this->_items);
    }

    /**
     * ログイン処理
     * @param Request $request
     * @return Redirector|Application|RedirectResponse
     */
    public function store(Request $request): Redirector|Application|RedirectResponse
    {
        $user     = $request->input('user', '');
        $password = $request->input('password', '');

        /* -- ID・PASSの認証処理 ---------------------*/
        if ($user === config('app.admin_auth_user') &&
            $password === config('app.admin_auth_pass')) {

            $request->session()->put('admin_auth', true);
            return redirect('/calender');
        }

        /* -- パスワード処理 ---------------------*/
        session()->flash('error', 'ID、パスワードが間違っています。');
        return redirect('/login');
    }
}
