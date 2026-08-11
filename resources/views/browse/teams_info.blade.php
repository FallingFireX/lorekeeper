@extends('layouts.app')

@section('title')
    Join the Team
@endsection

@section('content')
    {!! breadcrumbs(['Teams' => 'teams', 'Join the team' => 'join-the-team']) !!}

    <h1>Teams and Staff Applications</h1>
    <div class="text-center">
        <div class="card-body">
            @if (!$application_intro->text)
                <p>Below you can read about each team and what areas of the game they handle. You can also see which teams have their applications open!</p>
            @else
                {!! $application_intro->text !!}
            @endif
        </div>
        <div class="container mt-3">
            <div class="row justify-content-center mt-4 align-items-stretch">
                @foreach ($teams as $teams)
                    <div class="col-md-4 mb-4 d-flex">
                        <div class="card text-center flex-fill" style="border-width:0px">
                            <h5 class="mt-4">{!! $teams->name !!}</h5>
                            <p>{!! $teams->description !!}</p>
                            @if ($teams->apps_open)
                                Applications are open!
                                <a class="btn btn-primary mx-auto mt-auto" href="{{ url('applications/new?team_id=' . $teams->id) }}"><i class="fas fa-envelope"></i> Apply to Join</a>
                            @else
                                <span class="btn btn-warning mt-1 mx-auto mt-auto" data-toggle="tooltip">Applications Closed</span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection
