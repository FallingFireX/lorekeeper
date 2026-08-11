<p>This will hold the design approval request.</p>
{!! Form::open(['url' => 'admin/designs/edit/' . $request->id . '/hold']) !!}

<p>By marking this design update as held it will add it to the hold queue. If this update is to be held by a trainee please select the trainee.</p>
<div class="form-check mb-2">
    {!! Form::checkbox('holding_for_trainee', 1, 0, ['class' => 'form-check-input', 'id' => 'holding_for_trainee', 'data-toggle' => 'toggle']) !!}
    {!! Form::label('holding_for_trainee', 'Is this a hold for a trainee?', ['class' => 'form-check-label ml-2']) !!}
</div>
<div class="trainee-fields" style="display:none;">
    <p>This will mark the Design Request as a trainee claim.</p>
    <?php
    $trainees =
        ['' => 'Select Trainee'] +
        \App\Models\User\User::whereIn(
            'id',
            \App\Models\User\UserTeam::where('type', 'Trainee')
                ->pluck('user_id')
                ->toArray(),
        )
            ->orderBy('name')
            ->pluck('name', 'id')
            ->toArray();
    ?>
    {!! Form::select('trainee_id', $trainees, null, ['class' => 'form-control', 'placeholder' => 'Select Trainee']) !!}
</div>

<div class="text-right mt-3">
    {!! Form::submit('Hold Request', ['class' => 'btn btn-secondary']) !!}
</div>
{!! Form::close() !!}

<script>
    $(document).ready(function() {

        $('#holding_for_trainee').on('change', function() {
            if ($(this).is(':checked')) {
                $('.trainee-fields').show();
            } else {
                $('.trainee-fields').hide();
            }
        });

    });
</script>
