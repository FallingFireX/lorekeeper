@if ($category)
    {!! Form::open(['url' => 'admin/suggestions/categories/delete/' . $category->id]) !!}

    <p>You are about to delete the category <strong>{{ $category->name }}</strong>. This is not reversible. If suggestions have been submitted to this category, you wont be able to delete it.</p>
    <p>Are you sure you want to delete <strong>{{ $category->name }}</strong>?</p>

    <div class="text-right">
        {!! Form::submit('Delete Category', ['class' => 'btn btn-danger']) !!}
    </div>

    {!! Form::close() !!}
@else
    Invalid category selected.
@endif
