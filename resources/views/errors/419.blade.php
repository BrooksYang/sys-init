@extends('errors::layout')
<style>
    a:hover, a:focus { text-decoration: none }
</style>

@section('title', 'Page Expired')

@section('message')
    页面已过期
    <br/><br/>
    请刷新或重新<a href="{{ route('login') }}" >登录</a>
    <br/><br/>

@stop
