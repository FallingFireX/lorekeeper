@extends('layouts.app')

@section('title')
    Edit {{ $guild->name }}'s Ranks
@endsection

@section('sidebar')
    @include('guilds._sidebar')
@endsection

@section('content')
    {!! breadcrumbs([ucwords(__('guilds.guilds')) => __('guilds.guilds'), $guild->name => $guild->viewUrl, 'Edit Ranks' => 'edit-ranks']) !!}

    <div class="row align-items-end">
        <div class="col-md-8">
            <h1>Edit {{ $guild->name }}'s Members & Characters</h1>
            <p>Manage the users and characters in your {{ __('guilds.guild') }} below. Only {{ __('guilds.guild') }} owners and mods may edit the guild. Staff may manage members as well.</p>
        </div>
        <div class="col-md-4 text-right">
            <a href="#" class="btn add-members btn-primary"><i class="fas fa-envelope"></i> Invite Members</a>
            <a href="#" class="btn add-characters btn-primary"><i class="fas fa-plus"></i> Add Characters</a>
        </div>
    </div>

    <ul class="nav nav-tabs d-flex justify-content-start" id="manageTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="user-tab" data-toggle="tab" data-target="#user" type="button" role="tab" aria-controls="user" aria-selected="true">Users</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="character-tab" data-toggle="tab" data-target="#character" type="button" role="tab" aria-controls="character" aria-selected="false">Characters</button>
        </li>
    </ul>
    <div class="tab-content" id="myTabContent">
        <div class="tab-pane fade show active" id="user" role="tabpanel" aria-labelledby="user-tab">
            {!! Form::open(['url' => '/' . __('guilds.guilds') . '/' . $guild->id . '/manage-members', 'id' => 'guildSettingForm']) !!}
            {!! Form::hidden('manage-type', 'users') !!}

            <div class="row action-bar mt-2">
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('action', 'Bulk Action') !!}
                        {!! Form::select(
                            'action',
                            [
                                'remove' => 'Remove Members',
                                'update_rank' => 'Update Rank',
                            ],
                            null,
                            ['class' => 'form-control', 'placeholder' => 'Select bulk action...'],
                        ) !!}
                    </div>
                </div>
                <div class="col-md-4 hide" data-type="update_rank">
                    <div class="form-group">
                        {!! Form::label('user_rank', 'Rank to Assign') !!}
                        {!! Form::select('user_rank', $userRanks ?? [], null, ['class' => 'form-control', 'placeholder' => 'Select rank...']) !!}
                    </div>
                </div>
            </div>

            @if ($guild->members)
                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col">Select</th>
                            <th scope="col">User</th>
                            <th scope="col">Permissions</th>
                            <th scope="col">Rank</th>
                            <th scope="col">Reputation</th>
                            <th scope="col">Joined</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($guild->members as $member)
                            <tr>
                                <th scope="row">{!! Form::checkbox('user_ids[]', $member->user_id) !!}</th>
                                <td>{!! $member->user->displayName !!}</td>
                                <td>{{ $member->permissionsName }}</td>
                                <td>{{ $member->rank->name ?? 'None' }}</td>
                                <td>{{ $member->reputation }}</td>
                                <td>{!! pretty_date($member->joined_at) !!}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p>No current guild members.</p>
            @endif

            <div class="text-right mt-4">
                {!! Form::submit('Save Members', ['class' => 'btn btn-primary update-guild']) !!}
            </div>
            {!! Form::close() !!}
        </div>
        <div class="tab-pane fade" id="character" role="tabpanel" aria-labelledby="character-tab">
            {!! Form::open(['url' => '/' . __('guilds.guilds') . '/' . $guild->id . '/manage-members', 'id' => 'guildSettingForm']) !!}
            {!! Form::hidden('manage-type', 'characters') !!}

            <div class="row action-bar mt-2">
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('action', 'Bulk Action') !!}
                        {!! Form::select(
                            'action',
                            [
                                'remove' => 'Remove Characters',
                                'update_rank' => 'Update Rank',
                            ],
                            null,
                            ['class' => 'form-control', 'placeholder' => 'Select bulk action...'],
                        ) !!}
                    </div>
                </div>
                <div class="col-md-4 hide" data-type="update_rank">
                    <div class="form-group">
                        {!! Form::label('character_rank', 'Rank to Assign') !!}
                        {!! Form::select('character_rank', $characterRanks ?? [], null, ['class' => 'form-control', 'placeholder' => 'Select rank...']) !!}
                    </div>
                </div>
            </div>

            @if ($guild->characters)
                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col">Select</th>
                            <th scope="col">Character</th>
                            <th scope="col">Rank</th>
                            <th scope="col">Reputation</th>
                            <th scope="col">Joined</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($guild->characters as $character)
                            <tr>
                                <th scope="row">{!! Form::checkbox('character_ids[]', $character->character_id) !!}</th>
                                <td>{!! $character->character->displayName !!}</td>
                                <td>{{ $character->rank->name ?? 'None' }}</td>
                                <td>{{ $character->reputation }}</td>
                                <td>{!! pretty_date($character->joined_at) !!}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p>No current guild characters.</p>
            @endif

            <div class="text-right mt-4">
                {!! Form::submit('Save Characters', ['class' => 'btn btn-primary update-guild']) !!}
            </div>
            {!! Form::close() !!}
        </div>
    </div>
@endsection

@section('scripts')
    @parent
    <script>
        $(document).ready(function() {

            $('#action').change(function() {
                var action = $(this).val();
                var $container = $(this).closest('.action-bar');

                $container.find('[data-type]').addClass('hide');
                $container.find('[data-type="' + action + '"]').removeClass('hide');
            });

            $('.add-members').on('click', function(e) {
                e.preventDefault();
                loadModal("{{ url($guild->viewUrl) }}/members/add", 'Invite Members');
            });

            $('.add-characters').on('click', function(e) {
                e.preventDefault();
                loadModal("{{ url($guild->viewUrl) }}/characters/add", 'Add Characters');
            });

        });
    </script>
@endsection
