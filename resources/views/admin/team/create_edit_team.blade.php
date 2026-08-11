@extends('admin.layout')

@section('admin-title')
    {{ $teams->id ? 'Edit' : 'Create' }} Team
@endsection

@section('admin-content')
    {!! breadcrumbs(['Admin Panel' => 'admin', 'Teams' => 'admin/teams', ($teams->id ? 'Edit' : 'Create') . ' Teams' => $teams->id ? 'admin/teams/edit/' . $teams->id : 'admin/teams/create']) !!}

    <h1>{{ $teams->id ? 'Edit' : 'Create' }} Team
        @if ($teams->id)
            <a href="#" class="btn btn-danger float-right delete-team-button">Delete Team</a>
        @endif
    </h1>

    {!! Form::open(['url' => $teams->id ? 'admin/teams/edit/' . $teams->id : 'admin/teams/create', 'files' => true]) !!}

    <h3>Basic Information</h3>
    <p>Create a new team below! Team types are as follows:
        <br><b>Main team</b>: This is your basic team that will fit most situations.
        <br><b>Admin Accounts</b>: In the event you have group admin accounts in use on your site, you can create an admin account team to sort them.
        <br><b>Leadership</b>: This should be for your group development or leadership. This team will always show all members in the team page.

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                {!! Form::label('Name') !!}
                {!! Form::text('name', $teams->name, ['class' => 'form-control']) !!}
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                {!! Form::label('Type') !!}
                {!! Form::select('type', ['Main Team' => 'Main Team', 'Admin Accounts' => 'Admin Accounts', 'Leadership' => 'Leadership'], $teams->type, ['class' => 'form-control', 'placeholder' => 'Select Team Type']) !!}
            </div>
        </div>
    </div>

    <div class="form-group">
        {!! Form::label('Description (Optional)') !!}{!! add_help('This text shows on pages such as the team applications page where it describes what a team does') !!}
        {!! Form::textarea('description', $teams->description, ['class' => 'form-control wysiwyg']) !!}
    </div>

    <div class="form-group">
        {!! Form::label('Responsibilities (Optional)') !!}{!! add_help('This shows a team\'s responsibilities on the admin dashboard. If left empty, it wont show.') !!}
        {!! Form::textarea('responsibilities', $teams->responsibilities, ['class' => 'form-control wysiwyg']) !!}
    </div>

    <div class="form-group">
        {!! Form::checkbox('apps_open', 1, $teams->id ? $teams->apps_open : 0, ['class' => 'form-check-input', 'data-toggle' => 'toggle']) !!}
        {!! Form::label('apps_open', 'Applications Open?', ['class' => 'form-check-label ml-3']) !!} {!! add_help('Turning this on will open applications for this team. This does not create any announcement or news, you must do that yourself. This simply opens the queue.') !!}
    </div>

    <div class="text-right">
        {!! Form::submit($teams->id ? 'Edit' : 'Create', ['class' => 'btn btn-primary']) !!}
    </div>

    {!! Form::close() !!}
@endsection

@section('scripts')
    @parent
    <script>
        $(document).ready(function() {
            $('.delete-team-button').on('click', function(e) {
                e.preventDefault();
                loadModal("{{ url('admin/teams/delete') }}/{{ $teams->id }}", 'Delete Team');
            });
        });
    </script>
@endsection
