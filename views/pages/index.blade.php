@extends('adoadomin::layouts.master')

@section('content-header')
<h1>
    ModelName
</h1>
@stop

@section('breadcrumb')
<ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-database"></i> CRUDoado</a></li>
    <li class="active">ModelName</li>
</ol>
@stop

@section('content')
<div class="box">
    <div class="box-header">
        <div class="box-title"></div>
        <div class="box-tools">
            <div class="btn-group">
                <div class="input-group">
                    <input name="search" class="form-control pull-right" placeholder="{{ trans('crudoado::messages.search_input') }}" type="text">
                    <div class="input-group-btn">
                        <button class="btn btn-default"><i class="fa fa-search"></i></button>
                    </div>
                </div>
            </div>
            <div class="btn-group">
                <a href="{{ route('crudoado.model.create', 'ModelName') }}" class="btn btn-primary"><i class="fa fa-plus"></i> {{ trans('crudoado::messages.create_button') }}</a>
            </div>
        </div>
    </div>

    <div class="box-body table-responsive no-padding">
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>#</th>
                    <th>User</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Reason</th>
                    <th>{{ trans('crudoado::messages.actions_table_header') }}</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>183</td>
                    <td>John Doe</td>
                    <td>11-7-2014</td>
                    <td><span class="label label-success">Approved</span></td>
                    <td>Bacon ipsum dolor sit amet salami venison chicken flank fatback doner.</td>
                    <td>
                        <a href="{{ route('crudoado.model.show', ['ModelName', 1]) }}" class="btn btn-default btn-sm"><i class="fa fa-eye"></i> {{ trans('crudoado::messages.show_button') }}</a>
                        <a href="{{ route('crudoado.model.edit', ['ModelName', 1]) }}" class="btn btn-primary btn-sm"><i class="fa fa-pencil"></i> {{ trans('crudoado::messages.edit_button') }}</a>
                        <a href="#" class="btn btn-danger btn-sm"><i class="fa fa-trash-o"></i> {{ trans('crudoado::messages.delete_button') }}</a>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="box-footer clearfix">
        <ul class="pagination no-margin pull-right">
            <li><a href="#">«</a></li>
            <li><a href="#">1</a></li>
            <li><a href="#">2</a></li>
            <li><a href="#">3</a></li>
            <li><a href="#">»</a></li>
        </ul>
    </div>
</div>
@stop