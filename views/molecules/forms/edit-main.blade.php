<ul class="nav nav-tabs" role="tablist">
    <li role="presentation" class="active"><a href="#main" aria-controls="main" role="tab"
                                              data-toggle="tab">Main</a></li>
    @forelse($relations as $relation)
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

        @forelse($relations as $relationKey => $relation)
            @if($relation->getDisplayType() === Anavel\Crud\Abstractor\Eloquent\Relation\Relation::DISPLAY_TYPE_INLINE)
                @foreach($form[$relationKey] as $field)
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
            @endif
        @empty
        @endforelse
    </div>

    @forelse($relations as $relationKey => $relation)
        @if($relation->getDisplayType() === Anavel\Crud\Abstractor\Eloquent\Relation\Relation::DISPLAY_TYPE_TAB)
            <div role="tabpanel" class="tab-pane" id="{{ $relationKey }}">
                @foreach($form[$relationKey] as $field)
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
            </div>
        @endif
    @empty
    @endforelse
</div>