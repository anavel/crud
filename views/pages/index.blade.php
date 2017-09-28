@extends('anavel::layouts.master')

@section('content-header')
<h1>
    {{ $abstractor->getName() }}
</h1>
@stop

@section('breadcrumb')
<ol class="breadcrumb">
    <li><a href="{{ route('anavel-crud.home') }}"><i class="fa fa-database"></i> {{ config('anavel-crud.name') }}</a></li>
    <li class="active">{{ $abstractor->getName() }}</li>
</ol>
@stop

@section('content')

<?php
$config = $abstractor->getConfig();
$show = array_key_exists('show', $config) && ($config['show'] === true);
$authorize = array_key_exists('authorize', $config) && ($config['authorize'] === true);
$slug = $abstractor->getSlug();
?>

@include('anavel-crud::atoms.delete', [
    'modelSlug' => $slug,
    'modelId'   => '%id%'
])

<div class="box">
    <div class="box-header">
        <div class="box-header">
            <div class="row">
                <div class="col-sm-9">
                    <form method="get">
                        <input name="search" type="text" value="{{ Input::get('search') }}" class="form-control pull-right" placeholder="{{ trans('anavel-crud::messages.search_input') }}" />
                    </form>
                </div>

                <div class="col-sm-1 col-sm-offset-2">
                    @if ($authorize) @can('adminCreate', $abstractor->getInstance())
                    <a href="{{ route('anavel-crud.model.create', $slug) }}" class="btn btn-primary"><i class="fa fa-plus"></i> {{ trans('anavel-crud::messages.create_button') }}</a>
                    @endcan @else
                    <a href="{{ route('anavel-crud.model.create', $slug) }}" class="btn btn-primary"><i class="fa fa-plus"></i> {{ trans('anavel-crud::messages.create_button') }}</a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="box-body table-responsive">
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <?php $sortDirection = (Input::get('direction') === 'asc') ? 'desc' : 'asc'; ?>

                   @foreach ($abstractor->getListFields()['main'] as $field)
                    <th>
                        @if (strpos($field->getName(), '.') !== false)
                        {{ $field->presentation() }}
                        @else
                        <a href="{{ route('anavel-crud.model.index', [$slug, 'sort' => $field->getName(), 'direction' => $sortDirection]) }}">
                            {{ $field->presentation() }}
                        </a>
                        @endif
                    </th>
                    @endforeach

                    <th>{{ trans('anavel-crud::messages.actions_table_header') }}</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($items as $item)
                <tr>
                    @foreach ($abstractor->getListFields()['main'] as $field)
                    <td>{!! $abstractor->getFieldValue($item, $field->getName()) !!}</td>
                    @endforeach

                    <td>
                        {{-- Show if the model can be shown. It must be an explicit config value--}}
                        @if ($show) @can('adminView', $item)
                        <a href="{{ route('anavel-crud.model.show', [$slug, $item->getKey()]) }}" class="btn btn-default btn-sm"><i class="fa fa-eye"></i> {{ trans('anavel-crud::messages.show_button') }}</a>
                        @endcan @endif

                        {{-- Edit if the model has authorization and the user has permissions or if the model does not require authorization --}}
                        @if ($authorize) @can('adminUpdate', $item)
                        <a href="{{ route('anavel-crud.model.edit', [$slug, $item->getKey()]) }}" class="btn btn-primary btn-sm"><i class="fa fa-pencil"></i> {{ trans('anavel-crud::messages.edit_button') }}</a>
                        @endcan @else
                        <a href="{{ route('anavel-crud.model.edit', [$slug, $item->getKey()]) }}" class="btn btn-primary btn-sm"><i class="fa fa-pencil"></i> {{ trans('anavel-crud::messages.edit_button') }}</a>
                        @endif

                        {{-- Delete if the model has authorization and the user has permissions or if the model does not require authorization --}}
                        @if ($authorize) @can('adminDestroy', $item)
                        <a href="#" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#delete-modal" data-action="{{ route('anavel-crud.model.destroy', [$slug, $item->getKey()]) }}"><i class="fa fa-trash-o"></i> {{ trans('anavel-crud::messages.delete_button') }}</a>
                        @endcan @else
                        <a href="#" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#delete-modal" data-action="{{ route('anavel-crud.model.destroy', [$slug, $item->getKey()]) }}"><i class="fa fa-trash-o"></i> {{ trans('anavel-crud::messages.delete_button') }}</a>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="{{ count($abstractor->getListFields()['main']) + 1 }}" style="text-align: center;">{{ trans('anavel-crud::messages.empty_list') }}</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($items->total() > $items->perPage())
    <div class="box-footer clearfix">
        {!! with(new Anavel\Crud\View\Presenters\Paginator($items))->render() !!}
    </div>
    @endif
</div>
@stop

@section('footer-scripts')
    @parent
    <script src="{{ asset('vendor/anavel-crud/js/modals.js') }}" type="text/javascript"></script>
@stop
