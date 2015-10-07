@extends('adoadomin::layouts.master')

@section('content-header')
<h1>
    ModelName
    <small>Show</small>
</h1>
@stop

@section('breadcrumb')
<ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-database"></i> CRUDoado</a></li>
    <li><a href="#">ModelName</li>
    <li class="active">Show</li>
</ol>
@stop

@section('content')

@show