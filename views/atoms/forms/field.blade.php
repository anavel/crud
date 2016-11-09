@if($field instanceof FormManager\Containers\Group)
    @if(empty($panel))
        @foreach($field as $secondaryField)
            @include('anavel-crud::atoms.forms.field', ['field' => $secondaryField])
        @endforeach
    @else
        <div class="panel panel-default">
            <div class="panel-heading" role="tab">
                <h4 class="panel-title">
                    <a role="button" data-toggle="collapse" href="#{{ $relation->getName() . $key }}"
                       aria-expanded="true" aria-controls="{{ $relation->getName() . $key }}" id="label-{{ $relation->getName() . $key }}">
                        {{ ! empty($relation->getDisplay()) && ! empty($field[$relation->getDisplay()]) ? $field[$relation->getDisplay()]->val() : ucfirst($relation->getName()) . ' ' . $key }}
                    </a>
                </h4>
            </div>
            <div id="{{ $relationKey . $key }}" class="panel-collapse collapse in" role="tabpanel"
                 aria-labelledby="label-{{ $relationKey . $key }}">
                <div class="panel-body">
                    @foreach($field as $secondaryField)
                        @include('anavel-crud::atoms.forms.field', ['field' => $secondaryField, 'panel' => false])
                    @endforeach
                </div>
            </div>
        </div>
    @endif
@else

    <div class="form-group">
        @if($field->attr('type') != 'hidden')
            <div class="col-sm-2 text-right">
            <label for="{{ $field->attr('id') }}"
                   class="control-label">{{ $field->label->html() }}{{ $field->attr('required') ? ' *' : '' }}</label>
            </div>
        @endif
            @if($field instanceof FormManager\Fields\File)
            <div class="col-sm-8">
                {!! $field->input !!}
            </div>
            <div class="col-sm-2">

                <span class="input-group-btn">
                    @if ($canTakeFileFromUploads)
                        <a href="#" class="btn btn-primary" data-toggle="modal" data-target="#uploadsModal" data-input-id="{{$field->input->attr('id')}}" data-frame-url="{{route('anavel-uploads.modal.file-browser')}}">
                        <i class="fa fa-upload"></i> {{_('Get from uploads')}}
                    </a>

                    @endif
                @if (!empty($field->val()))
                    <a href="{{  url(config('anavel-crud.uploads_path')) . DIRECTORY_SEPARATOR .  $field->val() }}" target="_blank" class="btn btn-primary">
                        <i class="fa fa-eye"></i> Ver
                    </a>
                @endif
                </span>
            </div>
        @else
                <div class="col-sm-10">
                    {!! $field->input !!}
                </div>

            @endif

    </div>
@endif

