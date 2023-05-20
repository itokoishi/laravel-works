<?php
namespace App\Http\Controllers;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;

class LogoutController extends CommonController
{
    /**
     * ログアウト処理
     * @param Request $request
     * @return Redirector|Application|RedirectResponse
     */
    public function index(Request $request): Redirector|Application|RedirectResponse
    {
        $request->session()->put('admin_auth', false);
        return redirect('/login');
    }
}
