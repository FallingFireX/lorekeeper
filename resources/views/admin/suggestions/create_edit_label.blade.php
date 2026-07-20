@extends('admin.layout')

@section('admin-title')
    {{ $label->id ? 'Edit' : 'Create' }} Label
@endsection

@section('admin-content')
    {!! breadcrumbs(['Admin Panel' => 'admin', 'Labels' => 'admin/suggestions/labels', ($label->id ? 'Edit' : 'Create') . ' Label' => $label->id ? 'admin/suggestions/labels/edit/' . $label->id : 'admin/suggestions/labels/create']) !!}

    <h1>{{ $label->id ? 'Edit' : 'Create' }} Label
        @if ($label->id)
            ({!! $label->name !!})
            <a href="#" class="btn btn-danger float-right delete-label-button">Delete Label</a>
        @endif
    </h1>

    {!! Form::open(['url' => $label->id ? 'admin/suggestions/labels/edit/' . $label->id : 'admin/suggestions/labels/create', 'files' => true]) !!}

    <h3>Basic Information</h3>

    <div class="form-group">
        {!! Form::label('Name') !!}
        {!! Form::text('name', $label->name, ['class' => 'form-control']) !!}
    </div>
    
    <div class="form-group">
        {!! Form::label('Color (Hex code; optional)') !!}
        <div class="input-group cp">
            {!! Form::text('color', $label->color, ['class' => 'form-control']) !!}
            <span class="input-group-append">
                <span class="input-group-text colorpicker-input-addon"><i></i></span>
            </span>
        </div>
    </div>

    <div class="form-group">
        {!! Form::label('Description (Optional)') !!}
        {!! Form::textarea('description', $label->description, ['class' => 'form-control wysiwyg']) !!}
    </div>

    <div class="text-right">
        {!! Form::submit($label->id ? 'Edit' : 'Create', ['class' => 'btn btn-primary']) !!}
    </div>

    {!! Form::close() !!}

@endsection

@section('scripts')
    @parent
    <script>
        $(document).ready(function() {
            $('.delete-label-button').on('click', function(e) {
                e.preventDefault();
                loadModal("{{ url('admin/suggestions/labels/delete') }}/{{ $label->id }}", 'Delete Label');
            });
        });
    </script>
@endsection
