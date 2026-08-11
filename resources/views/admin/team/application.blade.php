@extends('admin.layout')

@section('admin-title')
    Application (#{{ $applications->id }})
@endsection

@section('admin-content')
    {!! breadcrumbs(['Admin Panel' => 'admin', 'Admin Applications' => 'admin/applications/pending', 'Application (#' . $applications->id . ')' => $applications->viewUrl]) !!}


    @if ($applications->status == 'pending')

        <h1>
            {{ $applications->id ? 'Application' : 'Claim' }} (#{{ $applications->id }})
            <span class="float-right badge badge-{{ $applications->status == 'pending' || $applications->status == 'Draft' ? 'secondary' : ($applications->status == 'Approved' ? 'success' : 'danger') }}">
                {{ $applications->status }}
            </span>
        </h1>

        <div class="mb-1">
            <div class="row">
                <div class="col-md-2 col-4">
                    <h5>User</h5>
                </div>
                <div class="col-md-10 col-8">{!! $applications->user->displayName !!}</div>
            </div>

            <div class="row">
                <div class="col-md-2 col-4">
                    <h5>Team:</h5>
                </div>
                <div class="col-md-10 col-8">{{ $teams->name }}</div>
            </div>

            <h2>Application</h2>
            <div class="card mb-3">
                <div class="card-body">{!! $applications->application !!}</div>
            </div>
            @if (Auth::check() && $applications->admin_message && ($applications->user_id == Auth::user()->id || Auth::user()->hasPower('edit_teams')))
                <h2>Admin Message ({!! $applications->staff->displayName !!})</h2>
                <div class="card mb-3">
                    <div class="card-body">
                        @if (isset($applications->admin_message))
                            {!! $applications->admin_message !!}
                        @else
                            {!! $applications->admin_message !!}
                        @endif
                    </div>
                </div>
            @endif

            <hr>

            Below you can <i><b>optionally</i></b> notify the player of <b>why</b> youve accepted/denied their application.
            <br><br>You can allow/disallow players to reply to this comment within site settings.

            <br>
            <div class="comments mt-3">
                @comments([
                    'model' => $applications,
                    'perPage' => 5,
                    'allow_dislikes' => false,
                ])
            </div>

            <div class="text-right">
                <i>Application notifications are currently turned <b>{{ $settings ? 'on' : 'off' }} </i>
                <br>
                {!! Form::open(['url' => route('admin.applications.post', $applications->id), 'method' => 'POST', 'class' => 'd-inline']) !!}@csrf
                <input type="hidden" name="status" value="accepted"><button type="submit" class="btn btn-success">Accept</button>
                {!! Form::close() !!}
                {!! Form::open(['url' => route('admin.applications.post', $applications->id), 'method' => 'POST', 'class' => 'd-inline']) !!}@csrf
                <input type="hidden" name="status" value="denied"><button type="submit" class="btn btn-danger">Deny</button>
                {!! Form::close() !!}
            </div>
        @else
            <div class="alert alert-success">This {{ $applications->id ? 'application' : 'claim' }} has already been {{ $applications->status }}!</div>
            @if ($applications->status == 'accepted')
                <div class="alert alert-secondary">You should reach out to this player via the contact methods they provided to invite them to any discord chat you might have, and
                    to give them instructions. Remember to update their user rank on the site!</div>
            @endif
            <h1>
                {{ $applications->id ? 'Application' : 'Claim' }} (#{{ $applications->id }})
                <span class="float-right badge badge-{{ $applications->status == 'pending' ? 'secondary' : ($applications->status == 'accepted' ? 'success' : 'danger') }}">
                    {{ $applications->status }}
                </span>
            </h1>

            <div class="mb-1">
                <div class="row">
                    <div class="col-md-2 col-4">
                        <h5>User</h5>
                    </div>
                    <div class="col-md-10 col-8">{!! $applications->user->displayName !!}</div>
                </div>

                <div class="row">
                    <div class="col-md-2 col-4">
                        <h5>Team:</h5>
                    </div>
                    <div class="col-md-10 col-8">{{ $teams->name }}</div>
                </div>

                <h2>Application</h2>
                <div class="card mb-3">
                    <div class="card-body">{!! $applications->application !!}</div>
                </div>
    @endif
@endsection
