<ul class="nav nav-tabs" role="tablist">
    <li role="presentation" class="active"><a href="#main" aria-controls="main" role="tab"
                                              data-toggle="tab">Main</a></li>
    @forelse($relations as $relation)
        @if ($relation instanceof \Illuminate\Support\Collection)
            <?php $relation = $relation->get('relation') ?>
        @endif
        @if($relation->getDisplayType() === Anavel\Crud\Abstractor\Eloquent\Relation\Relation::DISPLAY_TYPE_TAB)
            <li role="presentation"><a href="#{{ $relation->getName() }}"
                                       aria-controls="{{ $relation->getName() }}" role="tab"
                                       data-toggle="tab">{{ $relation->getPresentation() }}</a></li>
        @endif
    @empty
    @endforelse
</ul>

<div class="tab-content">
    <div role="tabpanel" class="tab-pane active" id="main">
        @unless(empty($form['main']))
        @foreach($form['main'] as $field)
            <div class="form-group">
                @if($field->attr('type') != 'hidden')
                    <label for="{{ $field->attr('id') }}"
                           class="col-sm-2 control-label">{{ $field->label->html() }}{{ $field->attr('required') ? ' *' : '' }}</label>
                @endif
                <div class="col-sm-10">
                    {!! $field->input !!}
                </div>
            </div>
        @endforeach
        @endunless

        @forelse($relations as $relationKey => $relation)
            @if ($relation instanceof \Illuminate\Support\Collection)
                @if($relation->get('relation')->getDisplayType() === Anavel\Crud\Abstractor\Eloquent\Relation\Relation::DISPLAY_TYPE_INLINE && ! empty($form[$relationKey]))
                    @foreach($form[$relationKey] as $field)
                        @include('anavel-crud::atoms.forms.field', ['field' => $field])
                    @endforeach
                @endif
            @else
                @if($relation->getDisplayType() === Anavel\Crud\Abstractor\Eloquent\Relation\Relation::DISPLAY_TYPE_INLINE && ! empty($form[$relationKey]))
                    @foreach($form[$relationKey] as $field)
                        @include('anavel-crud::atoms.forms.field', ['field' => $field])
                    @endforeach
                @endif
            @endif
        @empty
        @endforelse
    </div>

    @forelse($relations as $relationKey => $relation)
        @if ($relation instanceof \Illuminate\Support\Collection)
            @if($relation->get('relation')->getDisplayType() === Anavel\Crud\Abstractor\Eloquent\Relation\Relation::DISPLAY_TYPE_TAB && ! empty($form[$relationKey]))
                <div role="tabpanel" class="tab-pane" id="{{ $relationKey }}">
                    @foreach($form[$relationKey] as $field)
                        @include('anavel-crud::atoms.forms.field', ['field' => $field])
                    @endforeach
                </div>
            @endif
            {{--                @if(! $relation->get('secondaryRelations')->isEmpty())--}}
        @else
            @if($relation->getDisplayType() === Anavel\Crud\Abstractor\Eloquent\Relation\Relation::DISPLAY_TYPE_TAB && ! empty($form[$relationKey]))
                <div role="tabpanel" class="tab-pane" id="{{ $relationKey }}">
                    @foreach($form[$relationKey] as $field)
                        @include('anavel-crud::atoms.forms.field', ['field' => $field])
                    @endforeach
                </div>
            @endif
        @endif
    @empty
    @endforelse
</div>