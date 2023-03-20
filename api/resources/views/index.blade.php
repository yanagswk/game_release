@extends('layout.common')

@section('title', 'インデックスページ')
@section('keywords', 'キーワード1,キーワード2,キーワード3')
@section('description', 'インデックスページの説明文です')
@section('pageCss')
<link href="/css/star/index.css" rel="stylesheet">
@endsection

@include('layout.header')

{{-- @section('content')
    <p>このページはインデックスページです。</p>
    <p><img src="/img/star/star.png" width="100" alt="星画像"></p>
@endsection --}}

{{-- @include('layout.submenu') --}}

{{-- @include('layout.footer') --}}
