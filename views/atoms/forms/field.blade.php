@if($field instanceof FormManager\Containers\Group)
    @foreach($field as $secondaryField)
        @include('anavel-crud::atoms.forms.field', ['field' => $secondaryField])
    @endforeach
@else
    <div class="form-group {{ $field instanceof FormManager\Fields\File && ! empty($field->val()) ? 'input-group' : '' }}">
        @if($field->attr('type') != 'hidden')
            <label for="{{ $field->attr('id') }}"
                   class="col-sm-2 control-label">{{ $field->label->html() }}{{ $field->attr('required') ? ' *' : '' }}</label>
        @endif

        <div class="col-sm-10">
            {!! $field->input !!}
        </div>

            @if($field instanceof FormManager\Fields\File && ! empty($field->val()))

            <span class="input-group-btn"><a href="{{ $field->val() }}" target="_blank" class="btn btn-primary"><i class="glyphicon glyphicon-eye-open"></i></a></span>

            @endif
    </div>
@endif