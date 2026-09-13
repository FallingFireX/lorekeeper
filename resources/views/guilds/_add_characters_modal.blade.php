{!! Form::open(['url' => $guild->viewUrl . '/characters/add']) !!}
<p>This will add characters from guild members to the group. You may add up to 15 characters at a time.</p>
<div class="form-group">
    {!! Form::label('characters[]', 'Characters to Add') !!}
    {!! Form::select('characters[]', $characters, null, ['class' => 'form-control selectize', 'multiple', 'placeholder' => 'Select characters...']) !!}
</div>
<div class="text-right">
    {!! Form::submit('Add Character(s)', ['class' => 'btn btn-primary']) !!}
</div>
{!! Form::close() !!}
<script>
    $(document).ready(function() {
        $('.selectize').selectize();
    });
</script>
