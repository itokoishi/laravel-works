<!doctype html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <title>ポートフォリオ</title>
    <meta name="description" content="ポートフォリオ">
    <meta name="author" content="SitePoint">
    <link rel="shortcut icon" href="/css/favicon.ico">
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1,-scalable=no">

    <!--== CSS ===================-->
    <!-- bootstrap -->
    <link rel="stylesheet" href="//stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"
          integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T"
          crossorigin="anonymous">

    <!-- datepicker -->
    <link rel="stylesheet" href="/bootstrap-datepicker/css/bootstrap-datepicker3.css">
    <link rel="stylesheet" href="/bootstrap-datepicker/css/bootstrap-datepicker3.standalone.css">
    <link rel="stylesheet" href="/fontawesome/css/all.css">
    <!-- flat-ui -->
    <link rel="stylesheet" href="/flat-ui/css/flat-ui.css">
    <!-- remodal -->
    <link rel="stylesheet" href="/remodal/remodal.css">
    <link rel="stylesheet" href="/remodal/remodal-default-theme.css">

    <link type="text/css" rel="stylesheet" href="/css/reset.css">
    <link type="text/css" rel="stylesheet" href="/css/style.css?2022031801">

    <!--== ページCSS ===================-->
    @yield('page-css')

    <!--== JS ===================-->
    <!-- jquery -->
    <script src="//code.jquery.com/jquery-3.5.1.min.js"
            integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0="
            crossorigin="anonymous">
    </script>
    <!-- bootstrap -->
    <script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"
            integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa"
            crossorigin="anonymous">
    </script>
    <!-- flat-ui -->
    <script src="/flat-ui/scripts/flat-ui.js"></script>
    <!-- bootstrap-datepicker -->
    <script src="/bootstrap-datepicker/js/bootstrap-datepicker.min.js"></script>
    <script src="/bootstrap-datepicker/locales/bootstrap-datepicker.ja.min.js"></script>
    <!-- remodal -->
    <script src="/remodal/remodal.js"></script>

    <script>
        $(() => {
            /* -------------------------------------
            main アップ
           ------------------------------------- */
            $('main').attr('class', 'slide-up-active');

            /* -------------------------------------
           ハンバーガーメニュー
           ------------------------------------- */
            $(document).on('click', '#hamburger-btn', function () {
                console.log($(this).attr('id'));
                if ($(this).hasClass('active')) {
                    $(this).removeClass('active');
                    $('.btn-line01').attr('class', 'line line-nav01');
                    $('.btn-line02').attr('class', 'line line-nav02');
                    $('.btn-line03').attr('class', 'line line-nav03');
                    $('#menu').attr('class', 'menu-close');
                } else {
                    $(this).addClass('active');
                    $('.line-nav01').attr('class', 'line btn-line01');
                    $('.line-nav02').attr('class', 'line btn-line02');
                    $('.line-nav03').attr('class', 'line btn-line03');
                    $('#menu').attr('class', 'menu-open');
                }
                return false
            });
        });
    </script>

    <!--== ページJS ===================-->
@yield('page-js')
<body>

<header>
    <h1>ポートフォリオ</h1>

    <!--ハンバーガーメニューのボタン-->
    <a href="" id="hamburger-btn">
        <span class="line line-nav01"></span>
        <span class="line line-nav02"></span>
        <span class="line line-nav03"></span>
    </a>

    <nav id="menu">
        <ul>
            <li id="nav-header"></li>
            <li><a href="/calender">カレンダー</a></li>
            <li><a href="/shift">シフト管理</a></li>
            <li><a href="/staff/list">スタッフ管理</a></li>
            <li><a href="https://py.itokoishi.work" target="__blank">ヤフオク相場(python)</a></li>
        </ul>

        <a href="/log-out" id="log-out">ログアウト</a>
    </nav>
</header>

<div id="container">
    <main>
        <aside>
            <!--== ナビゲーション ===================-->
            <nav>
                <ul>
                    <li><a href="/calender">カレンダー</a></li>
                    <li><a href="/shift">シフト管理</a></li>
                    <li><a href="/staff/list">スタッフ管理</a></li>
                    <li><a href="https://py.itokoishi.work" target="__blank">ヤフオク相場(python)</a></li>
                </ul>

                <a href="/log-out" id="log-out">ログアウト</a>
            </nav>

        </aside>

        <!--== コンテンツ ===================-->
        <section id="main-content">
            @yield('main-contents')
        </section>

    </main>
</div>

@if(str_contains('calender', request()->path()))
<a href="" id="smart-register-bt">
    <i class="fas fa-plus"></i>
</a>
@endif
</body>
</html>
