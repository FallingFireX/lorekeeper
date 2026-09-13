@extends('layouts.app')

@section('title')
    New Submission
@endsection

@section('sidebar')
    @include('guilds._sidebar')
@endsection

@section('content')
    {!! breadcrumbs([ucwords(__('guilds.guilds')) => __('guilds.guilds'), $guild->name => $guild->viewUrl, 'Settings' => 'settings']) !!}

    <div class="row">
        <div class="col-md-10">
            <h1>Edit {{ $guild->name }}</h1>
            <p>Edit your {{ __('guilds.guild') }} below. Only {{ __('guilds.guild') }} owners and mods may edit the guild. Staff may edit your guild as well.</p>
        </div>
        @if (Auth::check() && (Auth::user()->id == $guild->owner_id || Auth::user()->isStaff))
            <div class="col-md-2 text-right">
                <a class="btn btn-danger disband">Disband Guild</a>
            </div>
        @endif
    </div>

    {!! Form::open(['url' => '/' . __('guilds.guilds') . '/' . $guild->id . '/edit', 'id' => 'guildSettingForm', 'files' => true]) !!}

    <div class="form-group">
        {!! Form::label('name', 'Name') !!}
        {!! Form::text('name', $guild->name, ['class' => 'form-control']) !!}
    </div>

    <div class="form-group">
        {!! Form::label('description', 'Description (Optional)') !!} {!! add_help('Give info about your ' . __('guilds.guild') . '! This can include images, tables, or other bootrap v4 content.') !!}
        {!! Form::textarea('description', $guild->description, ['class' => 'form-control wysiwyg']) !!}
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                {!! Form::label('Guild Logo (Optional)') !!} {!! add_help('A logo to represent your guild.') !!}
                <div class="custom-file">
                    {!! Form::label('logo', $guild->has_logo ? $guild->getLogoFileNameAttribute() : 'Choose file...', ['class' => 'custom-file-label']) !!}
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
        </div>
        <div class="col-md-6">
            <div class="form-group">
                {!! Form::label('Banner (Optional)') !!} {!! add_help('This banner is only shown on the guild\'s main page.') !!}
                <div class="custom-file">
                    {!! Form::label('banner', $guild->has_banner ? $guild->getBannerFileNameAttribute() : 'Choose file...', ['class' => 'custom-file-label']) !!}
                    {!! Form::file('banner', ['class' => 'custom-file-input']) !!}
                </div>
                <div class="text-muted">Recommended size: 800 x 400px</div>
                @if ($guild->has_banner)
                    <div class="form-check">
                        {!! Form::checkbox('remove_banner', 1, false, ['class' => 'form-check-input']) !!}
                        {!! Form::label('remove_banner', 'Remove current banner', ['class' => 'form-check-label']) !!}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                {!! Form::label('location', 'Location (Optional)') !!} {!! add_help('If your ' . __('guilds.guild') . ' resides in a specific location, enter it here!') !!}
                {!! Form::text('location', $guild->location, ['class' => 'form-control']) !!}
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('max_users', 'Maximum Players') !!} {!! add_help('The maximum players your ' . __('guilds.guild') . ' will accept. If your ' . __('guilds.guild') . ' is already at max, any applications will be rejected.') !!}
                {!! Form::number('max_users', $guild->max_users, ['class' => 'form-control', 'min' => 1, 'max' => $global_max_players]) !!}
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('max_characters', 'Maximum Characters') !!} {!! add_help('The maximum characters your ' . __('guilds.guild') . ' will accept. If your ' . __('guilds.guild') . ' is already at max, players attempting to add a character will not be able to.') !!}
                {!! Form::number('max_characters', $guild->max_characters, ['class' => 'form-control', 'min' => 1, 'max' => $global_max_characters]) !!}
            </div>
        </div>
    </div>

    <hr />

    <h4>Permissions & Other Settings</h4>
    <p>Settings below will enable or disable attributes of your {{ __('guilds.guild') }}. If you for example have items in the {{ __('guilds.guild') }}'s inventory - this will only disable players being able to add more items or view it. But owners and
        mods may still see it.</p>

    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                {!! Form::checkbox('open_new_users', 1, $guild->open_new_users, ['class' => 'form-check-input', 'data-toggle' => 'toggle']) !!}
                {!! Form::label('open_new_users', 'Open for Applications', ['class' => 'form-check-label ml-3']) !!} {!! add_help('Open for any players to submit applications.') !!}
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                {!! Form::checkbox('automatic_app_approval', 1, $guild->automatic_app_approval, ['class' => 'form-check-input', 'data-toggle' => 'toggle']) !!}
                {!! Form::label('automatic_app_approval', 'Automatically approve any Applications', ['class' => 'form-check-label ml-3']) !!} {!! add_help('If turned on, you won\'t need to accept any applications - they will automatically be accepted.') !!}
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                {!! Form::checkbox('open_inventory', 1, $guild->open_inventory, ['class' => 'form-check-input', 'data-toggle' => 'toggle']) !!}
                {!! Form::label('open_inventory', 'Open Inventory', ['class' => 'form-check-label ml-3']) !!} {!! add_help('Open the inventory to members.') !!}
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                {!! Form::checkbox('open_bank', 1, $guild->open_bank, ['class' => 'form-check-input', 'data-toggle' => 'toggle']) !!}
                {!! Form::label('open_bank', 'Open Bank', ['class' => 'form-check-label ml-3']) !!} {!! add_help('Open the bank to members.') !!}
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                {!! Form::checkbox('open_pets', 1, $guild->open_pets, ['class' => 'form-check-input', 'data-toggle' => 'toggle']) !!}
                {!! Form::label('open_pets', 'Open Pet Daycare', ['class' => 'form-check-label ml-3']) !!} {!! add_help('Open the pet daycare to members.') !!}
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                {!! Form::checkbox('open_armory', 1, $guild->open_armory, ['class' => 'form-check-input', 'data-toggle' => 'toggle']) !!}
                {!! Form::label('open_armory', 'Open Armory', ['class' => 'form-check-label ml-3']) !!} {!! add_help('Open the armory to members.') !!}
            </div>
        </div>
    </div>

    <div class="text-right">
        {!! Form::submit('Update Guild Settings', ['class' => 'btn btn-primary update-guild']) !!}
    </div>

    {!! Form::close() !!}

    <div class="card mt-4">
        <h3 class="card-header">Guild Moderators</h3>
        <div class="card-body">
            {!! Form::open(['url' => '/' . __('guilds.guilds') . '/' . $guild->id . '/edit/staff', 'id' => 'guildSettingForm']) !!}

            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('Guild Owner') !!}
                        {!! Form::select('owner_id', $members, $guild->owner_id, ['class' => 'form-control selectize']) !!}
                    </div>
                </div>
                <div class="col-md-9">
                    <div class="form-group">
                        {!! Form::label('Guild Moderators') !!}
                        {!! Form::select('mods[]', $members, $guild->mods()->pluck('user_id') ?? null, ['class' => 'form-control selectize', 'multiple']) !!}
                    </div>
                </div>
            </div>

            <div class="text-right">
                {!! Form::submit('Update Guild Staff', ['class' => 'btn btn-primary update-guild']) !!}
            </div>

            {!! Form::close() !!}
        </div>
    </div>

    <div class="modal fade" id="disbandModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <span class="modal-title h5 mb-0">Confirm Disbanding</span>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    {!! Form::open(['url' => '/' . __('guilds.guilds') . '/' . $guild->id . '/disband']) !!}
                    <p>This will disband the {{ __('guilds.guild') }} and remove all members and characters. This action cannot be undone.</p>
                    <div class="text-right">
                        {!! Form::submit('Disband Guild', ['class' => 'btn btn-danger', 'id' => 'disbandSubmit']) !!}
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    @parent
    <script>
        $(document).ready(function() {
            $('.selectize').selectize();
            var $disbandModal = $('#disbandModal');
            var $disbandButton = $('.disband');
            var $disbandSubmit = $('#disbandSubmit');

            $disbandButton.on('click', function(e) {
                e.preventDefault();
                $disbandModal.modal('show');
            });
        });
    </script>
@endsection
