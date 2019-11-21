@extends('errors::layout')
<style>
    a:hover, a:focus { text-decoration: none }
</style>

@section('title', 'Page Expired')

@section('message')
    REQUEST IS FORBIDDEN
    <br/><br/>
    非法请求 &nbsp;<a href="{{ URL::previous() }}" >返回</a>
    <br/><br/>
@stop
