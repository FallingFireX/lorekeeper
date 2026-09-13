{!! Form::open(['url' => $guild->viewUrl . '/members/add']) !!}
<p>This will <strong>invite</strong> users. Some users may not receive invites if they have disabled invitations.</p>
<div class="form-group">
    {!! Form::label('users[]', 'Users to Invite') !!}
    {!! Form::select('users[]', $users, null, ['class' => 'form-control selectize', 'multiple', 'placeholder' => 'Select characters...']) !!}
</div>
<div class="text-right">
    {!! Form::submit('Invite User(s)', ['class' => 'btn btn-primary']) !!}
</div>
{!! Form::close() !!}
<script>
    $(document).ready(function() {
        $('.selectize').selectize();
    });
</script>
