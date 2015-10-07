@extends('adoadomin::layouts.master')

@section('content-header')
<h1>
    ModelName
    <small>Create</small>
</h1>
@stop

@section('breadcrumb')
<ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-database"></i> CRUDoado</a></li>
    <li><a href="#">ModelName</li>
    <li class="active">Create</li>
</ol>
@stop

@section('content')
<div class="box">
    <form class="form-horizontal">
        <div class="box-header">
            <h3 class="box-title">ModelName</h3>
            <div class="box-tools">
                <a href="#"><i class="fa fa-arrow-left"></i> Back</a>
                <button type="submit" class="btn tn-primary pull-right"><i class="fa fa-plus"></i> Create</button>
            </div>
        </div>

        <div class="box-body">
            <div class="form-group">
                <label for="inputEmail3" class="col-sm-2 control-label">Email</label>
                <div class="col-sm-10">
                    <input class="form-control" id="inputEmail3" placeholder="Email" type="email">
                </div>
            </div>
        </div>

        <div class="box-footer clearfix">
            <a href="#" class="btn btn-default">Cancel</a>
            <button type="submit" class="btn btn-primary pull-right"><i class="fa fa-plus"></i> Create</button>
        </div>
    </form>
</div>
@show