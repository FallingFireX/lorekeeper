@if ($guild)
    {!! Form::open(['url' => 'admin/guilds/delete/' . $guild->id]) !!}

    <p>You are about to delete the guild <strong>{{ $guild->name }}</strong>.</p>
    <p>This will remove the guild entirely
    <p>Are you sure you want to delete <strong>{{ $item->name }}</strong>?</p>

    <div class="text-right">
        {!! Form::submit('Delete Item', ['class' => 'btn btn-danger']) !!}
    </div>

    {!! Form::close() !!}
@else
    Invalid item selected.
@endif
