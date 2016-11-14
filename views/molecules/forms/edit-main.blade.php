<ul class="nav nav-tabs" role="tablist">
    <li role="presentation" class="active"><a href="#main" aria-controls="main" role="tab"
                                              data-toggle="tab">{{ transcrud('Main') }}</a></li>
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
<div class="tab-content pad">
    <div role="tabpanel" class="tab-pane active" id="main">
        @unless(empty($form['main']))
        @foreach($form['main'] as $field)
                @include('anavel-crud::atoms.forms.field', ['field' => $field])
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
                    @foreach($form[$relationKey] as $key => $field)
                        @include('anavel-crud::atoms.forms.field', ['field' => $field, 'panel' => true, 'key' => $key, 'relation' => $relation->get('relation')])
                    @endforeach
                </div>
            @endif
        @else
            @if($relation->getDisplayType() === Anavel\Crud\Abstractor\Eloquent\Relation\Relation::DISPLAY_TYPE_TAB && ! empty($form[$relationKey]))
                <div role="tabpanel" class="tab-pane" id="{{ $relationKey }}">
                    @foreach($form[$relationKey] as $key => $field)
                        @include('anavel-crud::atoms.forms.field', ['field' => $field, 'relation' => $relation, 'panel' => true, 'key' => $key])
                    @endforeach
                </div>
            @endif
        @endif
    @empty
    @endforelse
</div>

    <div id="uploadsModal" class="modal fade " role="dialog">
        <div class="modal-dialog modal-lg">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Uploads moda</h4>
                </div>
                <div class="modal-body">
                    <iframe style="width: 100%; border: none; height: 500px"> </iframe>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>

        </div>
    </div>

@section('footer-scripts')
    @parent
    <script type="text/javascript">

        var activeSelector = undefined;
        $(function () {


            $('#uploadsModal').on('show.bs.modal', function(event)
            {
                var modal = $(this);
                var button = $(event.relatedTarget); // Button that triggered the modal
                activeSelector = button;
                var frameUrl =button.data('frameUrl');
                modal.find('iframe').attr('src',frameUrl);
                var inputId = activeSelector.data('inputId');
                var inputFile  = $('#'+ inputId);
                inputFile.replaceWith( inputFile.val('').clone(true));

            });
        });

        window.fileSelector = function(url)
        {
            $('#uploadsModal').modal('hide');
            var inputId = activeSelector.data('inputId');

            var inputFile  = $('#'+ inputId);
            var inputName = inputFile.attr('name').replace(/[\[\]']+/g,'#');
            var name = 'uploaded-content['+inputName+']';
            var filePath = url;
            var n = filePath.indexOf('{{config('anavel-crud.uploads_path')}}') + '{{config('anavel-crud.uploads_path')}}'.length;
            filePath = filePath.substr(n);
            inputFile.parent().find('.selected-from-uploads').remove();
            var tpl = '<div class="selected-from-uploads" style="margin-top: 5px">' +
                    '       <img src="'+url+'" style="width: 100px" /> ' +
                    '       <br/> ' +
                    '       <em>{{_('Get from uploads')}} <a href="#" class="delete-upload"><i class="fa fa-trash"/></a></em>' +
                    '   <input type="hidden" value="'+filePath+'" name="'+name+'" />'
            '</div>';
            inputFile.parent().append(tpl);

            inputFile.on('click',function (){
                $(this).parent().find('.selected-from-uploads').remove();

            } );

            $('.delete-upload').on('click',function (e){
                e.preventDefault();
                $(this).parents('.selected-from-uploads').remove();
                return false;
            });

        }
    </script>

@stop
