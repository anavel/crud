@extends('adoadomin::layouts.master')

@section('content-header')
<h1>
    {{ $abstractor->getName() }}
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
                <a href="{{ route('crudoado.model.create', $abstractor->getSlug()) }}" class="btn btn-primary"><i class="fa fa-plus"></i> {{ trans('crudoado::messages.create_button') }}</a>
            </div>
        </div>
    </div>

    <div class="box-body table-responsive no-padding">
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    @foreach ($abstractor->getListFields() as $field)
                    <th>{{ $field->presentation() }}</th>
                    @endforeach
                    <th>{{ trans('crudoado::messages.actions_table_header') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($items as $item)
                <tr>
                    @foreach ($abstractor->getListFields() as $field)
                    <td>{{ $item->attributes[$field->getName()] }}</td>
                    @endforeach
                    <td>
                        <a href="{{ route('crudoado.model.show', [$abstractor->getSlug(), 1]) }}" class="btn btn-default btn-sm"><i class="fa fa-eye"></i> {{ trans('crudoado::messages.show_button') }}</a>
                        <a href="{{ route('crudoado.model.edit', [$abstractor->getSlug(), 1]) }}" class="btn btn-primary btn-sm"><i class="fa fa-pencil"></i> {{ trans('crudoado::messages.edit_button') }}</a>
                        <a href="#" class="btn btn-danger btn-sm"><i class="fa fa-trash-o"></i> {{ trans('crudoado::messages.delete_button') }}</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="{{ count($abstractor->getListFields()) + 1 }}" style="text-align: center;">{{ trans('crudoado::messages.empty_list') }}</td>
                </tr>
                @endforelse
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