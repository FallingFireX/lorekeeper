@extends('home.layout')

@section('home-title')
    Application
@endsection

@section('home-content')
    {!! breadcrumbs(['Applications' => 'applications', 'Application (#' . $applications->id . ')' => $applications->viewUrl]) !!}

    <h1 class="mb-2">
        Application #{{ $applications->id }}
    </h1>
    <div class="row mt-4">
        <div class="col-md-3">
            <h5>Application submitted to:</h5>
        </div>
        <div class="col-md-3">
            <h5>{{ $teams->name }}</h5>
        </div>
        <div class="col-md-6">
            <div class="text-right">
                <h1>
                    <span class="float-right badge badge-{{ $applications->status == 'pending' ? 'secondary' : ($applications->status == 'accepted' ? 'success' : 'danger') }}">
                        {{ $applications->status }}
                    </span>
                </h1>
            </div>
        </div>
    </div>
    <h3 class="mt-4">Application contents</h3>
    <div class="card mb-3">
        <div class="card-body">{!! $applications->application !!}</div>
    </div>

    <!-- I make comments read only here under the assumption that most groups dont want sad or upset players replying to them about denied applications.
     Setting "read_only" to false will allow replies as normal. -->
    <div class="comments">
        @comments([
            'model' => $applications,
            'perPage' => 5,
            'allow_dislikes' => false,
            'read_only' => $is_read_only,
        ])
    </div>
@endsection
