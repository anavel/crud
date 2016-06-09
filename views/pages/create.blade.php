@extends('anavel::layouts.master')

@section('content-header')
    <h1>
        {{ $abstractor->getName() }}
        <small>{{ trans('anavel-crud::messages.create_title') }}</small>
    </h1>
@stop

@section('breadcrumb')
    <ol class="breadcrumb">
        <li><a href="{{ route('anavel-crud.home') }}"><i class="fa fa-database"></i> {{ config('anavel-crud.name') }}
            </a></li>
        <li><a href="{{ route('anavel-crud.model.index', $abstractor->getSlug()) }}">{{ $abstractor->getName() }}</a>
        </li>
        <li class="active">{{ trans('anavel-crud::messages.create_title') }}</li>
    </ol>
@stop

@section('content')
    <div class="box">
        {!! $form->openHtml() !!}
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        <div class="box-header">
            <div class="box-title">
                <a href="{{ URL::previous() }}"><i
                            class="fa fa-arrow-left"></i> {{ trans('anavel-crud::messages.back_button') }}</a>
            </div>
            <div class="box-tools">
                <button type="submit" class="btn btn-primary pull-right"><i
                            class="fa fa-save"></i> {{ trans('anavel-crud::messages.save_button') }}</button>
            </div>
        </div>

        <div class="box-body">
            @include('anavel-crud::molecules.forms.edit-main')
        </div>

        <div class="box-footer clearfix">
            <a href="#" class="btn btn-default">{{ trans('anavel-crud::messages.cancel_button') }}</a>
            <button type="submit" class="btn btn-primary pull-right"><i
                        class="fa fa-save"></i> {{ trans('anavel-crud::messages.save_button') }}</button>
        </div>
        {!! $form->closeHtml() !!}
    </div>
@stop

@section('head')
    @parent

    <link href="{{ asset('vendor/anavel/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css') }}" rel="stylesheet"
          type="text/css"/>
    <link href="{{ asset('vendor/anavel-crud/plugins/selectizejs/css/selectize.bootstrap3.css') }}" rel="stylesheet"
          type="text/css" />
    <link href="{{ asset('vendor/anavel-crud/plugins/pikaday/css/pikaday.css') }}" rel="stylesheet"
          type="text/css" />

@stop

@section('footer-scripts')
    @parent

    <script src="{{ asset('vendor/anavel/plugins/ckeditor/ckeditor.js') }}"></script>
    <script src="{{ asset('vendor/anavel/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js') }}"></script>
    <script src="{{ asset('vendor/anavel-crud/js/app.js') }}" type="text/javascript"></script>
    <script src="{{ asset('vendor/anavel-crud/plugins/selectizejs/js/standalone/selectize.min.js') }}"
            type="text/javascript"></script>
    <script src="{{ asset('vendor/anavel-crud/plugins/momentjs/moment.min.js') }}"
            type="text/javascript"></script>
    <script src="{{ asset('vendor/anavel-crud/plugins/pikaday/pikaday.js') }}"
            type="text/javascript"></script>
    <script src="{{ asset('vendor/anavel-crud/plugins/pikaday/plugins/pikaday.jquery.js') }}"
            type="text/javascript"></script>
@stop
