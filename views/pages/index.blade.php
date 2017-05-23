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

@include('anavel-crud::atoms.delete', [
    'modelSlug' => $abstractor->getSlug(),
    'modelId'   => '%id%'
])

<div class="box">
    <div class="box-header">
        <div class="btn-group">
            <form method="get">
                <div class="input-group">
                    <input name="search" type="text" value="{{ Input::get('search') }}" class="form-control pull-right" placeholder="{{ trans('anavel-crud::messages.search_input') }}">
                    <div class="input-group-btn">
                        <button type="submit" class="btn btn-default"><i class="fa fa-search"></i></button>
                    </div>
                </div>
            </form>
        </div>

        <div class="btn-group">
            @if (array_key_exists('authorize', $config = $abstractor->getConfig()) && $config['authorize'] === true)
                @can('adminCreate', $abstractor->getInstance())
                <a href="{{ route('anavel-crud.model.create', $abstractor->getSlug()) }}" class="btn btn-primary"><i class="fa fa-plus"></i> {{ trans('anavel-crud::messages.create_button') }}</a>
                @endcan
            @else
                <a href="{{ route('anavel-crud.model.create', $abstractor->getSlug()) }}" class="btn btn-primary"><i class="fa fa-plus"></i> {{ trans('anavel-crud::messages.create_button') }}</a>
            @endif
        </div>
    </div>

    <div class="box-body table-responsive">
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                   @foreach ($abstractor->getListFields()['main'] as $field)
                        <?php
                        $isSorting = false;
                        $sortDirection = 'asc';
                        
                        if (Input::get('sort') === $field->getName()) {
                            $isSorting = true;

                            if (Input::get('direction') === 'asc') {
                                $sortDirection = 'desc';
                            }
                        }
                        ?>
                    <th>
                        <a href="{{ route('anavel-crud.model.index', [$abstractor->getSlug(), 'sort' => $field->getName(), 'direction' => $sortDirection]) }}">
                            {{ $field->presentation() }}
                        </a>
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
                        @if (array_key_exists('show', $config = $abstractor->getConfig()) && $config['show'] === true)
                            @can('adminView', $item)
                            <a href="{{ route('anavel-crud.model.show', [$abstractor->getSlug(), $item->getKey()]) }}" class="btn btn-default btn-sm"><i class="fa fa-eye"></i> {{ trans('anavel-crud::messages.show_button') }}</a>
                            @endcan
                        @endif
                        {{-- Edit if the model has authorization and the user has permissions or if the model does not require authorization --}}
                        @if (array_key_exists('authorize', $config = $abstractor->getConfig()) && $config['authorize'] === true)
                            @can('adminUpdate', $item)
                            <a href="{{ route('anavel-crud.model.edit', [$abstractor->getSlug(), $item->getKey()]) }}" class="btn btn-primary btn-sm"><i class="fa fa-pencil"></i> {{ trans('anavel-crud::messages.edit_button') }}</a>
                            @endcan
                        @else
                        <a href="{{ route('anavel-crud.model.edit', [$abstractor->getSlug(), $item->getKey()]) }}" class="btn btn-primary btn-sm"><i class="fa fa-pencil"></i> {{ trans('anavel-crud::messages.edit_button') }}</a>
                        @endif
                        {{-- Delete if the model has authorization and the user has permissions or if the model does not require authorization --}}
                        @if (array_key_exists('authorize', $config = $abstractor->getConfig()) && $config['authorize'] === true)
                            @can('adminDestroy', $item)
                                <a href="#" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#delete-modal" data-action="{{ route('anavel-crud.model.destroy', [$abstractor->getSlug(), $item->getKey()]) }}"><i class="fa fa-trash-o"></i> {{ trans('anavel-crud::messages.delete_button') }}</a>
                            @endcan
                        @else
                           <a href="#" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#delete-modal" data-action="{{ route('anavel-crud.model.destroy', [$abstractor->getSlug(), $item->getKey()]) }}"><i class="fa fa-trash-o"></i> {{ trans('anavel-crud::messages.delete_button') }}</a>
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
