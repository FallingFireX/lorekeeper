@extends('admin.layout')

@section('admin-title')
    {{ $category->id ? 'Edit' : 'Create' }} Category
@endsection

@section('admin-content')
    {!! breadcrumbs(['Admin Panel' => 'admin', 'categories' => 'admin/suggestions/categories', ($category->id ? 'Edit' : 'Create') . ' Category' => $category->id ? 'admin/suggestions/categories/edit/' . $category->id : 'admin/suggestions/categories/create']) !!}

    <h1>{{ $category->id ? 'Edit' : 'Create' }} Category
        @if ($category->id)
            ({!! $category->name !!})
            <a href="#" class="btn btn-danger float-right delete-category-button">Delete Category</a>
        @endif
    </h1>

    {!! Form::open(['url' => $category->id ? 'admin/suggestions/categories/edit/' . $category->id : 'admin/suggestions/categories/create', 'files' => true]) !!}

    <h3>Basic Information</h3>

    <div class="form-group">
        {!! Form::label('Name') !!}
        {!! Form::text('name', $category->name, ['class' => 'form-control']) !!}
    </div>
    
    <div class="form-group">
        {!! Form::label('Description (Optional)') !!}
        {!! Form::textarea('description', $category->description, ['class' => 'form-control wysiwyg']) !!}
    </div>

    <div class="form-group">
        {!! Form::checkbox('is_visible', 1, $category->id ? $category->is_visible : 0, ['class' => 'form-check-input', 'data-toggle' => 'toggle']) !!}
        {!! Form::label('is_visible', 'Is Visible', ['class' => 'form-check-label ml-3']) !!} {!! add_help('When off, this hides this category from members when submitting new suggestions. Existing suggestions can still be viewed') !!}
    </div>

    <div class="text-right">
        {!! Form::submit($category->id ? 'Edit' : 'Create', ['class' => 'btn btn-primary']) !!}
    </div>

    {!! Form::close() !!}

@endsection

@section('scripts')
    @parent
    <script>
        $(document).ready(function() {
            $('.delete-category-button').on('click', function(e) {
                e.preventDefault();
                loadModal("{{ url('admin/suggestions/categories/delete') }}/{{ $category->id }}", 'Delete Category');
            });
        });
    </script>
@endsection
