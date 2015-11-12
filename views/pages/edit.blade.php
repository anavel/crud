@extends('adoadomin::layouts.master')

@section('content-header')
<h1>
    {{ $abstractor->getName() }}
    <small>{{ trans('crudoado::messages.edit_title') }}</small>
</h1>
@stop

@section('breadcrumb')
<ol class="breadcrumb">
    <li><a href="{{ route('crudoado.home') }}"><i class="fa fa-database"></i> {{ config('crudoado.name') }}</a></li>
    <li><a href="{{ route('crudoado.model.index', $abstractor->getSlug()) }}">{{ $abstractor->getName() }}</a></li>
    <li class="active">{{ trans('crudoado::messages.edit_title') }}</li>
</ol>
@stop

@section('content')
<div class="box">
    {!! $form->openHtml() !!}
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        <input type="hidden" name="_method" value="PUT">
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
                @if($field->attr('type') != 'hidden')
                    <label for="{{ $field->attr('id') }}" class="col-sm-2 control-label">{{ $field->label->html() }}</label>
                @endif
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

@section('head')
    @parent

    <link href="{{ asset('vendor/adoadomin/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css') }}" rel="stylesheet" type="text/css" />
@stop

@section('footer-scripts')
    @parent

    <script src="{{ asset('vendor/adoadomin/plugins/ckeditor/ckeditor.js') }}"></script>
    <script src="{{ asset('vendor/adoadomin/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js') }}"></script>

    <script src="{{ asset('vendor/crudoado/js/app.js') }}" type="text/javascript"></script>
@stop