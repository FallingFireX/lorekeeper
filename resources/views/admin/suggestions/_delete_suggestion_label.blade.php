@if ($category)
    {!! Form::open(['url' => 'admin/suggestions/labels/delete/' . $label->id]) !!}

    <p>You are about to delete the Label <strong>{{ $label->name }}</strong>. This is not reversible. If this label exists on any suggestion, you wont be able to delete it.</p>
    <p>Are you sure you want to delete <strong>{{ $label->name }}</strong>?</p>

    <div class="text-right">
        {!! Form::submit('Delete Label', ['class' => 'btn btn-danger']) !!}
    </div>

    {!! Form::close() !!}
@else
    Invalid label selected.
@endif
