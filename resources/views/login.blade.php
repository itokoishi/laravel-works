<!doctype html>
<html lang="ja">
<head>
    <!--== meta ===================-->
    <meta charset="utf-8">
    <title>ポートフォリオ|ログイン</title>
    <meta name="description" content="ポートフォリオ|ログイン">
    <meta name="author" content="ポートフォリオ|ログイン">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">

    <!--== css ===================-->
    <link rel="stylesheet" href="//stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css"
          integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/themes/base/jquery-ui.min.css">
    <link rel="stylesheet" href="/css/reset.css">
    <link rel="stylesheet" href="/css/login.css">
</head>
<body>
<div id="container">
    <main>
        <!--== コンテンツ ===================-->
        <section>
            <h1>ポートフォリオログイン</h1>

            <div id="login-box">

                @if(session()->has('error'))
                    <p class="alert alert-danger">{{session()->get('error')}}</p>
                @endif

                <form id="login-form" action="" method="post">
                    <input type="text" class="form-control" name="user" id="user" placeholder="ID"/>
                    <input type="password" class="form-control" name="password" id="password" placeholder="パスワード"/>
                    　@csrf
                    <button class="btn btn-success btn-block">ログイン</button>
                </form>
            </div>
        </section>
    </main>
</div>
</body>
</html>
