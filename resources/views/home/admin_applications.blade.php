@extends('home.layout')

@section('home-title')
    My Applications
@endsection

@section('home-content')
    {!! breadcrumbs(['Applications' => 'applications']) !!}


    <h1>
        Submitted applications
    </h1>
    <p>These are applications you've submitted to join the admin/staff team. Generally, you should avoid submitting several applications to the same team unless
        the group permits it, or you've made a major mistake in your first application.</p>

    <div class="text-left">
        <div class="mb-4 logs-table">
            <div class="logs-table-header">
                <div class="row">
                    <div class="col-md-3 font-weight-bold">
                        <div class="logs-table-cell">Team</div>
                    </div>
                    <div class="col-md-4 font-weight-bold">
                        <div class="logs-table-cell">Submission Date</div>
                    </div>
                    <div class="col-md-3 font-weight-bold">
                        <div class="logs-table-cell">Status</div>
                    </div>
                    <div class="col-md-2 font-weight-bold">
                        <div class="logs-table-cell"></div>
                    </div>
                </div>
            </div>
            <div class="logs-table-body">
                @foreach ($applications as $application)
                    <div class="logs-table-row">
                        <div class="row flex-wrap">
                            <div class="col-md-3">
                                <div class="logs-table-cell">
                                    {{ $teams->name }}
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="logs-table-cell">
                                    {!! pretty_date($application->created_at) !!}
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="logs-table-cell">
                                    <span class="btn btn-{{ $application->status == 'pending' ? 'secondary' : ($application->status == 'accepted' ? 'success' : 'danger') }} btn-sm py-0 px-1">
                                        {{ ucfirst($application->status) }}
                                    </span>
                                </div>
                            </div>

                            {{-- Details link --}}
                            <div class="col-md-2">
                                <div class="logs-table-cell">
                                    <a href="{{ url('applications/' . $application->id) }}" class="btn btn-primary btn-sm py-0 px-1">Details</a>
                                </div>
                            </div>

                        </div>
                    </div>
                @endforeach
            </div>

        </div>
    @endsection
