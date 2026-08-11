@extends('admin.layout')

@section('admin-title')
    Team Index
@endsection

@section('admin-content')
    {!! breadcrumbs(['Admin Panel' => 'admin', 'Team Index' => 'admin/team']) !!}

    <h1>
        Team Index</h1>

    <p>Here you can see all teams youve created for your admins.</p>

    <div class="text-right mb-3">
        <a class="btn btn-primary" href="{{ url('admin/teams/create') }}"><i class="fas fa-plus"></i> Create New Team</a>
    </div>

    <div class="mb-4 logs-table">
        <div class="logs-table-header">
            <div class="row">
                <div class="col-12 col-md-3">
                    <div class="logs-table-cell">name</div>
                </div>
                <div class="col-4 col-md-3">
                    <div class="logs-table-cell">Type</div>
                </div>
                <div class="col-4 col-md-3">
                    <div class="logs-table-cell">Applications Open?</div>
                </div>
            </div>
        </div>
        <div class="logs-table-body">
            @foreach ($teams as $team)
                <div class="logs-table-row">
                    <div class="row flex-wrap">
                        <div class="col-12 col-md-3 ">
                            <div class="logs-table-cell">{{ $team->name }}</a></div>
                        </div>
                        <div class="col-12 col-md-3 ">
                            <div class="logs-table-cell">{{ $team->type }}</a></div>
                        </div>
                        <div class="col-12 col-md-3 ">
                            <div class="logs-table-cell">
                                @if (!$team->apps_open)
                                    Closed
                                @else
                                    open
                                @endif
                                </a>
                            </div>
                        </div>
                        <div class="col-12 col-md-1">
                            <div class="logs-table-cell"><a href="{{ url('admin/teams/edit/' . $team->id) }}" class="btn btn-primary py-0 px-1 w-100">Edit</a></div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection
