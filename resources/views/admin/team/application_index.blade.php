@extends('admin.layout')

@section('admin-title')
    Applications Queue
@endsection

@section('admin-content')
    {!! breadcrumbs(['Admin Panel' => 'admin', 'Admin Applications' => 'admin/applications/pending']) !!}

    <h1>
        Applications Queue
    </h1>

    <ul class="nav nav-tabs mb-3">
        <li class="nav-item">
            <a class="nav-link {{ set_active('admin/applications/pending') }} {{ set_active('admin/applications/pending') }}" href="{{ url('admin/applications/pending') }}">Pending</a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ set_active('admin/applications/accepted') }}" href="{{ url('admin/applications/accepted') }}">Accepted</a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ set_active('admin/applications/denied') }}" href="{{ url('admin/applications/denied') }}">Denied</a>
        </li>
    </ul>

    <div class="mb-4 logs-table">
        <div class="logs-table-header">
            <div class="row">
                <div class="col-12 col-md-3">
                    <div class="logs-table-cell">User</div>
                </div>
                <div class="col-4 col-md-2">
                    <div class="logs-table-cell">Team</div>
                </div>
                <div class="col-4 col-md-2">
                    <div class="logs-table-cell">Status</div>
                </div>
                <div class="col-4 col-md-2">
                    <div class="logs-table-cell">Details</div>
                </div>
                <div class="col-4 col-md-3">
                    <div class="logs-table-cell">Submitted on</div>
                </div>
            </div>
        </div>
        <div class="logs-table-body">
            @foreach ($applications as $application)
                <div class="logs-table-row">
                    <div class="row flex-wrap">
                        <div class="col-12 col-md-3 ">
                            <div class="logs-table-cell">{!! $application->user->displayName !!}</a></div>
                        </div>
                        <div class="col-12 col-md-2 ">
                            <div class="logs-table-cell">{{ $teams->name }}</a></div>
                        </div>
                        <div class="col-12 col-md-2 ">
                            <div class="logs-table-cell">
                                <span class="btn btn-{{ $application->status == 'pending' ? 'secondary' : ($application->status == 'accepted' ? 'success' : 'danger') }} btn-sm py-0 px-1">
                                    {{ ucfirst($application->status) }}
                                </span>
                            </div>
                        </div>
                        <div class="col-3 col-md-2">
                            <div class="logs-table-cell"><a href="{{ $application->adminUrl }}" class="btn btn-primary btn-sm py-0 px-1">Details</a></div>
                        </div>
                        <div class="col-3 col-md-3">
                            <div class="logs-table-cell">
                                {!! pretty_date($application->created_at) !!}
                            </div>
                        </div>

                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection
