@extends('adoadomin::layouts.master')

@section('content-header')
<h1>
    {{ $abstractor->getName() }}
    <small>Create</small>
</h1>
@stop

@section('breadcrumb')
<ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-database"></i> CRUDoado</a></li>
    <li><a href="#">ModelName</a></li>
    <li class="active">Create</li>
</ol>
@stop

@section('content')
<div class="box">
    {!! $form->openHtml() !!}
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        <div class="box-header">
            <div class="box-title">
                <a href="{{ URL::previous() }}"><i class="fa fa-arrow-left"></i> {{ trans('crudoado::messages.back_button') }}</a>
            </div>
            <div class="box-tools">
                <button type="submit" class="btn btn-primary pull-right"><i class="fa fa-save"></i> {{ trans('crudoado::messages.save_button') }}</button>
            </div>
        </div>

        <div class="box-body">
            @foreach ($form as $field)
            <div class="form-group">
                <label for="{{ $field->attr('id') }}" class="col-sm-2 control-label">{{ $field->label->html() }}</label>
                <div class="col-sm-10">
                    {!! $field->input !!}
                </div>
            </div>
            @endforeach
        </div>

        <div class="box-footer clearfix">
            <a href="#" class="btn btn-default">{{ trans('crudoado::messages.cancel_button') }}</a>
            <button type="submit" class="btn btn-primary pull-right"><i class="fa fa-save"></i> {{ trans('crudoado::messages.save_button') }}</button>
        </div>
    {!! $form->closeHtml() !!}
</div>
@stop