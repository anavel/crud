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
        <div class="box-title"></div>
        <div class="box-tools">
            <div class="btn-group">
                <form method="get" action="">
                    <div class="input-group">
                        <input name="search" type="text" value="{{ Input::get('search') }}" class="form-control pull-right" placeholder="{{ trans('anavel-crud::messages.search_input') }}">
                        <div class="input-group-btn">
                            <button type="submit" class="btn btn-default"><i class="fa fa-search"></i></button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="btn-group">
                <a href="{{ route('anavel-crud.model.create', $abstractor->getSlug()) }}" class="btn btn-primary"><i class="fa fa-plus"></i> {{ trans('anavel-crud::messages.create_button') }}</a>
            </div>
        </div>
    </div>

    <div class="box-body table-responsive no-padding">
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
                    <td>{!! $item->getAttribute($field->getName()) !!}</td>
                    @endforeach
                    <td>
                        {{--<a href="{{ route('anavel-crud.model.show', [$abstractor->getSlug(), $item->getKey()]) }}" class="btn btn-default btn-sm"><i class="fa fa-eye"></i> {{ trans('anavel-crud::messages.show_button') }}</a>--}}
                        <a href="{{ route('anavel-crud.model.edit', [$abstractor->getSlug(), $item->getKey()]) }}" class="btn btn-primary btn-sm"><i class="fa fa-pencil"></i> {{ trans('anavel-crud::messages.edit_button') }}</a>
                        {{--<a href="#" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#delete-modal"><i class="fa fa-trash-o"></i> {{ trans('anavel-crud::messages.delete_button') }}</a>--}}
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