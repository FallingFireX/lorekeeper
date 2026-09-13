@extends('admin.layout')

@section('admin-title')
    $guild->id ? Edit Guild
@endsection

@section('admin-content')
    {!! breadcrumbs(['Admin Panel' => 'admin', 'Guilds' => 'admin/guilds', ($guild->id ? 'Edit' : 'Create') . ' Guild' => $guild->id ? 'admin/guilds/edit/' . $guild->id : 'admin/guilds/create']) !!}

    <h1>{{ $guild->id ? 'Edit' : 'Create' }} Guild
        @if ($guild->id)
            <a href="#" class="btn btn-outline-danger float-right delete-guild-button">Delete guild</a>
            <a href="#" class="btn btn-outline-secondary float-right disband-guild-button mr-2">Disband guild</a>
        @endif
    </h1>

    {!! Form::open(['url' => $guild->id ? 'admin/guilds/edit/' . $guild->id : 'admin/guilds/create', 'files' => true]) !!}

    <h3>Basic Information</h3>

    <div class="form-group">
        {!! Form::label('Name') !!}
        {!! Form::text('name', $guild->name, ['class' => 'form-control']) !!}
    </div>
    <div class="form-group">
        {!! Form::label('Owner') !!}
        {!! Form::select('artist_id', $userOptions, $guild && $guild->owner_id ? $guild->owner_id : null, ['class' => 'form-control mr-2 selectize', 'placeholder' => 'Select a User']) !!}
                        
    </div>

    <div class="form-group">
        {!! Form::label('Guild Logo (optional)') !!} {!! add_help('This is the logo that will show on the guild page') !!}
        <div class="custom-file">
            {!! Form::label('logo', 'Choose file...', ['class' => 'custom-file-label']) !!}
            {!! Form::file('logo', ['class' => 'custom-file-input']) !!}
        </div>
        <div class="text-muted">Recommended size: 200px x 200px</div>
        @if ($guild->has_logo)
            <div class="form-check">
                {!! Form::checkbox('remove_logo', 1, false, ['class' => 'form-check-input']) !!}
                {!! Form::label('remove_logo', 'Remove current logo', ['class' => 'form-check-label']) !!}
            </div>
        @endif
    </div>


    <div class="form-group">
        {!! Form::label('Description (Optional)') !!}
        {!! Form::textarea('description', $guild->description, ['class' => 'form-control wysiwyg']) !!}
    </div>


    <div class="col-md form-group">
        {!! Form::hidden('status', 'inactive') !!}
        {!! Form::checkbox('status', 'active', ($guild->id ? $guild->status == 'active' : true), ['class' => 'form-check-input', 'data-toggle' => 'toggle']) !!}
        {!! Form::label('status', 'Is Active?', ['class' => 'form-check-label ml-3']) !!} 
        {!! add_help('If this is off, users will not be able to view information for the item/it will be hidden from view. This is overridden by the item being owned at any point by anyone on the site.') !!}
    </div>

    

<div class="row">
            <div class="col-md">
                <div class="form-group">
                    {!! Form::checkbox('open_new_users', 1, $guild->id ? $guild->open_new_users : 1, ['class' => 'form-check-input', 'data-toggle' => 'toggle']) !!}
                    {!! Form::label('open_new_users', 'Open to new users?', ['class' => 'form-check-label ml-3']) !!} {!! add_help('If this is off, no one new can join the guild. This will not impact those already in the guild') !!}
                </div>
            </div>
            <div class="col-md">
                <div class="form-group">
                    {!! Form::label( 'max_users', 'Max users', 'Quantity') !!}
                    {!! Form::text('max_users', $guild->max_users, ['class' => 'form-control']) !!}
                </div>
            </div>
            <div class="col-md">
                <div class="form-group">
                    {!! Form::label( 'max_characters', 'Max characters', 'Quantity') !!}
                    {!! Form::text('max_characters', $guild->max_characters, ['class' => 'form-control']) !!}
                </div>
            </div>
        </div>
  

    <div class="text-right">
        {!! Form::submit($guild->id ? 'Edit' : 'Create', ['class' => 'btn btn-primary']) !!}
    </div>

    {!! Form::close() !!}


@endsection

@section('scripts')
    @parent
    <script>
        $(document).ready(function() {
            $('.selectize').selectize();

            $('#promptsList').selectize({
                maxItems: 10
            });

            $('.delete-guild-button').on('click', function(e) {
                e.preventDefault();
                loadModal("{{ url('admin/guilds/delete') }}/{{ $guild->id }}", 'Delete Guild');
            });

            $('.disband-guild-button').on('click', function(e) {
                e.preventDefault();
                loadModal("{{ url('admin/guilds/delete') }}/{{ $guild->id }}", 'Disband Guild');
            });
        });
    </script>
@endsection
