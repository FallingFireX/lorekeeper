@extends('layouts.app')

@section('title')
    Edit {{ $guild->name }}'s Ranks
@endsection

@section('sidebar')
    @include('guilds._sidebar')
@endsection

@section('content')
    {!! breadcrumbs([ucwords(__('guilds.guilds')) => __('guilds.guilds'), $guild->name => $guild->viewUrl, 'Edit Ranks' => 'edit-ranks']) !!}

    <h1>Edit {{ $guild->name }}'s Ranks</h1>
    <p>Edit your {{ __('guilds.guild') }} below. Only {{ __('guilds.guild') }} owners and mods may edit the guild. Staff may edit your guild as well.</p>

    {!! Form::open(['url' => '/' . __('guilds.guilds') . '/' . $guild->id . '/edit-ranks', 'id' => 'guildSettingForm', 'files' => true]) !!}

    <div class="ranks">
        <div class="card mb-4">
            <h3 class="card-header">User Ranks</h3>
            <div class="card-body">
                <div class="user-ranks rank-list">
                    @if ($guild->ranks)
                        @foreach ($guild->ranks->where('for_user', 1) as $rank)
                            <div class="rank-row">
                                <div class="d-flex direction-row">
                                    <div class="form-group w-25 mr-2">
                                        {!! Form::label('rank_name', 'Name') !!}
                                        {!! Form::text('user_ranks[0][rank_name]', $rank->name, ['class' => 'form-control']) !!}
                                    </div>
                                    <div class="form-group mr-2">
                                        {!! Form::label('rank_threshold[0]', 'Reputation Threshold') !!}
                                        {!! Form::number('user_ranks[0][rank_threshold]', $rank->required_reputation ?? 0, ['class' => 'form-control', 'min' => 0]) !!}
                                    </div>
                                    <div class="form-group w-50 mr-2">
                                        {!! Form::label('description[0]', 'Description (Optional)') !!} {!! add_help('Give info about your ' . __('guilds.guild') . '! This can include images, tables, or other bootrap v4 content.') !!}
                                        {!! Form::text('user_ranks[0][description]', $rank->description ?? '', ['class' => 'form-control']) !!}
                                    </div>
                                    <div class="form-group mr-2">
                                        {!! Form::label('Rank Icon (Optional)') !!} {!! add_help('Add an optional icon to distinguish the rank.') !!}
                                        <div class="custom-file">
                                            {!! Form::label('user_ranks[0][icon]', $rank->has_image ? $rank->getRankImageName($rank->id) : 'Choose file...', ['class' => 'custom-file-label']) !!}
                                            {!! Form::file('user_ranks[0][icon]', ['class' => 'custom-file-input']) !!}
                                        </div>
                                        <div class="text-muted">Recommended size: 50px x 50px</div>
                                        @if ($rank->has_image)
                                            <div class="form-check">
                                                {!! Form::checkbox('user_ranks[0][remove_icon]', 1, false, ['class' => 'form-check-input']) !!}
                                                {!! Form::label('user_ranks[0][remove_icon]', 'Remove current icon', ['class' => 'form-check-label']) !!}
                                            </div>
                                        @endif
                                    </div>
                                    <a href="#" class="btn btn-danger remove-rank align-self-center">-</a>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="rank-row">
                            <div class="d-flex direction-row">
                                <div class="form-group w-25 mr-2">
                                    {!! Form::label('rank_name', 'Name') !!}
                                    {!! Form::text('user_ranks[0][rank_name]', null, ['class' => 'form-control']) !!}
                                </div>
                                <div class="form-group mr-2">
                                    {!! Form::label('rank_threshold[0]', 'Reputation Threshold') !!}
                                    {!! Form::number('user_ranks[0][rank_threshold]', null, ['class' => 'form-control', 'min' => 0]) !!}
                                </div>
                                <div class="form-group w-50 mr-2">
                                    {!! Form::label('description[0]', 'Description (Optional)') !!} {!! add_help('Give info about your ' . __('guilds.guild') . '! This can include images, tables, or other bootrap v4 content.') !!}
                                    {!! Form::text('user_ranks[0][description]', null, ['class' => 'form-control']) !!}
                                </div>
                                <div class="form-group mr-2">
                                    {!! Form::label('Rank Icon (Optional)') !!} {!! add_help('Add an optional icon to distinguish the rank.') !!}
                                    <div class="custom-file">
                                        {!! Form::label('user_ranks[0][icon]', 'Choose file...', ['class' => 'custom-file-label']) !!}
                                        {!! Form::file('user_ranks[0][icon]', ['class' => 'custom-file-input']) !!}
                                    </div>
                                    <div class="text-muted">Recommended size: 50px x 50px</div>
                                    @if ($guild->has_logo)
                                        <div class="form-check">
                                            {!! Form::checkbox('user_ranks[0][remove_icon]', 1, false, ['class' => 'form-check-input']) !!}
                                            {!! Form::label('user_ranks[0][remove_icon]', 'Remove current icon', ['class' => 'form-check-label']) !!}
                                        </div>
                                    @endif
                                </div>
                                <a href="#" class="btn btn-danger remove-rank align-self-center">-</a>
                            </div>
                        </div>
                    @endif
                    <div class="text-right add-rank-container">
                        <a href="#" class="btn btn-primary add-rank">Add Rank</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <h3 class="card-header">Character Ranks</h3>
            <div class="card-body">
                <div class="character-ranks rank-list">
                    @if ($guild->ranks)
                        @foreach ($guild->ranks->where('for_character', 1) as $rank)
                            <div class="rank-row">
                                <div class="d-flex direction-row">
                                    <div class="form-group w-25 mr-2">
                                        {!! Form::label('rank_name', 'Name') !!}
                                        {!! Form::text('character_ranks[0][rank_name]', $rank->name, ['class' => 'form-control']) !!}
                                    </div>
                                    <div class="form-group mr-2">
                                        {!! Form::label('rank_threshold[0]', 'Reputation Threshold') !!}
                                        {!! Form::number('character_ranks[0][rank_threshold]', $rank->required_reputation ?? 0, ['class' => 'form-control', 'min' => 0]) !!}
                                    </div>
                                    <div class="form-group w-50 mr-2">
                                        {!! Form::label('description[0]', 'Description (Optional)') !!} {!! add_help('Give info about your ' . __('guilds.guild') . '! This can include images, tables, or other bootrap v4 content.') !!}
                                        {!! Form::text('character_ranks[0][description]', $rank->description ?? '', ['class' => 'form-control']) !!}
                                    </div>
                                    <div class="form-group mr-2">
                                        {!! Form::label('Rank Icon (Optional)') !!} {!! add_help('Add an optional icon to distinguish the rank.') !!}
                                        <div class="custom-file">
                                            {!! Form::label('character_ranks[0][icon]', $rank->has_image ? $rank->rankImageName : 'Choose file...', ['class' => 'custom-file-label']) !!}
                                            {!! Form::file('character_ranks[0][icon]', ['class' => 'custom-file-input']) !!}
                                        </div>
                                        <div class="text-muted">Recommended size: 50px x 50px</div>
                                        @if ($rank->has_image)
                                            <div class="form-check">
                                                {!! Form::checkbox('character_ranks[0][remove_icon]', 1, false, ['class' => 'form-check-input']) !!}
                                                {!! Form::label('character_ranks[0][remove_icon]', 'Remove current icon', ['class' => 'form-check-label']) !!}
                                            </div>
                                        @endif
                                    </div>
                                    <a href="#" class="btn btn-danger remove-rank align-self-center">-</a>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="rank-row">
                            <div class="d-flex direction-row">
                                <div class="form-group w-25 mr-2">
                                    {!! Form::label('rank_name', 'Name') !!}
                                    {!! Form::text('character_ranks[0][rank_name]', null, ['class' => 'form-control']) !!}
                                </div>
                                <div class="form-group mr-2">
                                    {!! Form::label('rank_threshold[0]', 'Reputation Threshold') !!}
                                    {!! Form::number('character_ranks[0][rank_threshold]', null, ['class' => 'form-control', 'min' => 0]) !!}
                                </div>
                                <div class="form-group w-50 mr-2">
                                    {!! Form::label('description[0]', 'Description (Optional)') !!} {!! add_help('Give info about your ' . __('guilds.guild') . '! This can include images, tables, or other bootrap v4 content.') !!}
                                    {!! Form::text('character_ranks[0][description]', null, ['class' => 'form-control']) !!}
                                </div>
                                <div class="form-group mr-2">
                                    {!! Form::label('Rank Icon (Optional)') !!} {!! add_help('Add an optional icon to distinguish the rank.') !!}
                                    <div class="custom-file">
                                        {!! Form::label('character_ranks[0][icon]', 'Choose file...', ['class' => 'custom-file-label']) !!}
                                        {!! Form::file('character_ranks[0][icon]', ['class' => 'custom-file-input']) !!}
                                    </div>
                                    <div class="text-muted">Recommended size: 50px x 50px</div>
                                    @if ($guild->has_logo)
                                        <div class="form-check">
                                            {!! Form::checkbox('character_ranks[0][remove_icon]', 1, false, ['class' => 'form-check-input']) !!}
                                            {!! Form::label('character_ranks[0][remove_icon]', 'Remove current icon', ['class' => 'form-check-label']) !!}
                                        </div>
                                    @endif
                                </div>
                                <a href="#" class="btn btn-danger remove-rank align-self-center">-</a>
                            </div>
                        </div>
                    @endif
                    <div class="text-right add-rank-container">
                        <a href="#" class="btn btn-primary add-rank">Add Rank</a>
                    </div>
                </div>
            </div>
        </div>
    </div>



    <div class="text-right mt-4">
        {!! Form::submit('Update', ['class' => 'btn btn-primary update-guild']) !!}
    </div>

    {!! Form::close() !!}
@endsection

@section('scripts')
    @parent
    <script>
        $(document).ready(function() {

            //Add a new rank
            $('.add-rank').click(function(e) {
                e.preventDefault();
                var rankRow = $(this).closest('.rank-list').find('.rank-row:first').clone();
                rankRow.find('input').val('');
                var index = $(this).closest('.rank-list').find('.rank-row').length;
                rankRow.find('input, label').each(function() {
                    if ($(this).attr('name')) {
                        var name = $(this).attr('name');
                        name = name.replace(/\[\d+\]/, '[' + index + ']');
                        $(this).attr('name', name);
                    }
                    if ($(this).attr('for')) {
                        var forAttr = $(this).attr('for');
                        forAttr = forAttr.replace(/\[\d+\]/, '[' + index + ']');
                        $(this).attr('for', forAttr);
                    }
                });
                rankRow.find('.custom-file label').text('Choose file...');

                $(rankRow).insertBefore($(this).closest('.add-rank-container'));
            });

            //Remove a rank row
            $('.ranks').on('click', '.remove-rank', function(e) {
                e.preventDefault();
                var count = $(this).closest('.rank-list').find('.rank-row').length;
                if (count > 1) {
                    $(this).parents('.rank-row').remove();
                }
            });

        });
    </script>
@endsection
