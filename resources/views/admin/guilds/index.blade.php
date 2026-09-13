@extends('admin.layout')

@section('admin-title')
    Guilds
@endsection

@section('admin-content')
    {!! breadcrumbs(['Admin Panel' => 'admin', 'Guilds' => 'admin/guilds']) !!}

    <h1>Guilds</h1>

    <p>Here you can find all guilds created by players</p>

    <div class="text-right mb-3">
        <a class="btn btn-primary" href="{{ url('admin/guilds/create') }}"><i class="fas fa-plus"></i> Create New Guild</a>
    </div>

    <div>
        {!! Form::open(['method' => 'GET', 'class' => 'form-inline justify-content-end']) !!}
        <div class="form-group mr-3 mb-3">
            {!! Form::text('name', Request::get('name'), ['class' => 'form-control', 'placeholder' => 'Name']) !!}
        </div>
        <div class="form-group mb-3">{!! Form::submit('Search', ['class' => 'btn btn-primary']) !!}</div>
        {!! Form::close() !!}
    </div>

    @if (!count($guilds))
        <p>No guilds found.</p>
    @else
        {!! $guilds->render() !!}
        <div class="mb-4 logs-table">
            <div class="logs-table-header">
                <div class="row">
                    <div class="col-4 col-md-2">
                        <div class="logs-table-cell">Name</div>
                    </div>
                    <div class="col-4 col-md-3">
                        <div class="logs-table-cell">Owner</div>
                    </div>
                    <div class="col-4 col-md-2">
                        <div class="logs-table-cell">Active</div>
                    </div>
                    <div class="col-4 col-md-2">
                        <div class="logs-table-cell">Created</div>
                    </div>
                </div>
            </div>
            <div class="logs-table-body">
                @foreach ($guilds as $guild)
                    <div class="logs-table-row">
                        <div class="row flex-wrap">
                            <div class="col-5 col-md-2 text-truncate">
                                <div class="logs-table-cell">
                                    {{ $guild->name }}
                                </div>
                            </div>
                            <div class="col-5 col-md-3">
                                <div class="logs-table-cell">
                                    {!! $guild->owner->DisplayName !!}
                                </div>
                            </div>
                            <div class="col-2 col-md-2">
                                <div class="logs-table-cell">
                                    {!! $guild->status === 'active' 
                                        ? '<i class="text-success fas fa-check"></i>' 
                                        : '<i class="text-danger fas fa-times"></i>' 
                                    !!}
                                </div>
                            </div>
                            
                            <div class="col-4 col-md-2">
                                <div class="logs-table-cell">
                                    {!! $guild->created_at ? pretty_date($guild->created_at) : '-' !!}
                                </div>
                            </div>
                            
                            <div class="col-3 col-md-2 text-right">
                                <div class="logs-table-cell">
                                    <a href="{{ url('admin/guilds/edit/' . $guild->id) }}" class="btn btn-primary py-0 px-2">Edit</a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {!! $guilds->render() !!}

        <div class="text-center mt-4 small text-muted">{{ $guilds->total() }} result{{ $guilds->total() == 1 ? '' : 's' }} found.</div>
    @endif

@endsection

@section('scripts')
    @parent
@endsection