<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel Vite React</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body>
        <div id="app"></div>
        <div>
            <a href="/vue">Vueへ</a>
        </div>
    </body>
</html>

{{-- <!DOCTYPE HTML>
<html lang="ja">
<head>
<meta charset="UTF-8">
<title>@yield('title')｜nodoame.net</title>
<meta name="description" itemprop="description" content="ページごとの説明文を設定します">
<meta name="keywords" itemprop="keywords" content="キーワード1,キーワード2,キーワード3">
<link href="/css/star/layout.css" rel="stylesheet">
@yield('pageCss')
</head>
<body>

@yield('header')

<div class="contents">
    <!-- コンテンツ -->
    <div class="main">
        @yield('content')
    </div>

    <!-- 共通メニュー -->
    <div class="sub">
        @yield('submenu')
    </div>
</div>

@yield('footer')
</body>
</html> --}}
