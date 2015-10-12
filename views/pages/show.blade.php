@extends('adoadomin::layouts.master')

@section('content-header')
<h1>
    {{ $abstractor->getName() }}
    <small>Show</small>
</h1>
@stop

@section('breadcrumb')
<ol class="breadcrumb">
    <li><a href="{{ route('crudoado.home') }}"><i class="fa fa-database"></i> {{ config('crudoado.name') }}</a></li>
    <li><a href="{{ route('crudoado.model.index', $abstractor->getSlug()) }}">{{ $abstractor->getName() }}</a></li>
    <li class="active">Show</li>
</ol>
@stop

@section('content')
<div class="box">
    <div class="box-header">
        <div class="box-title">
            <a href="{{ URL::previous() }}"><i class="fa fa-arrow-left"></i> {{ trans('crudoado::messages.back_button') }}</a>
        </div>
        <div class="box-tools">
            <div class="btn-group">
                <a href="{{ route('crudoado.model.create', $abstractor->getSlug()) }}" class="btn btn-default"><i class="fa fa-plus"></i> {{ trans('crudoado::messages.create_button') }}</a>
            </div>
            <div class="btn-group">
                <a href="{{ route('crudoado.model.edit', [$abstractor->getSlug(), $item->getKey()]) }}" class="btn btn-primary"><i class="fa fa-pencil"></i> {{ trans('crudoado::messages.edit_button') }}</a>
            </div>
            <div class="btn-group">
                <a href="#" class="btn btn-danger"><i class="fa fa-trash-o"></i> {{ trans('crudoado::messages.delete_button') }}</a>
            </div>
        </div>
    </div>

    <div class="box-body">
        <dl class="dl-horizontal">
            @foreach ($abstractor->getDetailFields() as $field)
            <dt>{{ $field->presentation() }}</dt>
            <dd>{!! $item->getAttribute($field->name()) !!}</dd>
            @endforeach
        </dl>
    </div>
</div>
@stop