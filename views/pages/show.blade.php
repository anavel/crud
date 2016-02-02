@extends('anavel::layouts.master')

@section('content-header')
<h1>
    {{ $abstractor->getName() }}
    <small>{{ trans('anavel-crud::messages.show_title') }}</small>
</h1>
@stop

@section('breadcrumb')
<ol class="breadcrumb">
    <li><a href="{{ route('anavel-crud.home') }}"><i class="fa fa-database"></i> {{ config('anavel-crud.name') }}</a></li>
    <li><a href="{{ route('anavel-crud.model.index', $abstractor->getSlug()) }}">{{ $abstractor->getName() }}</a></li>
    <li class="active">{{ trans('anavel-crud::messages.show_title') }}</li>
</ol>
@stop

@section('content')

@include('anavel-crud::atoms.delete', [
    'modelSlug' => $abstractor->getSlug(),
    'modelId'   => $item->getKey()
])

<div class="box">
    <div class="box-header">
        <div class="box-title">
            <a href="{{ URL::previous() }}"><i class="fa fa-arrow-left"></i> {{ trans('anavel-crud::messages.back_button') }}</a>
        </div>
        <div class="box-tools">
            <div class="btn-group">
                <a href="{{ route('anavel-crud.model.create', $abstractor->getSlug()) }}" class="btn btn-default"><i class="fa fa-plus"></i> {{ trans('anavel-crud::messages.create_button') }}</a>
            </div>
            <div class="btn-group">
                <a href="{{ route('anavel-crud.model.edit', [$abstractor->getSlug(), $item->getKey()]) }}" class="btn btn-primary"><i class="fa fa-pencil"></i> {{ trans('anavel-crud::messages.edit_button') }}</a>
            </div>
            <div class="btn-group">
                <a href="#" class="btn btn-danger"  data-toggle="modal" data-target="#delete-modal"><i class="fa fa-trash-o"></i> {{ trans('anavel-crud::messages.delete_button') }}</a>
            </div>
        </div>
    </div>

    <div class="box-body">
        <dl class="dl-horizontal">
            @foreach ($abstractor->getDetailFields() as $fieldGroupName => $fieldGroup)
                @foreach($fieldGroup as $field)
                    <dt>{{ $field->presentation() }}</dt>
                    <dd>{!! $item->getAttribute($field->getName()) !!}</dd>
                @endforeach
            @endforeach
        </dl>
    </div>
</div>
@stop