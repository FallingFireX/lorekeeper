@extends('layouts.app')

@section('title')
    {{ ucwords(__('guilds.guilds')) }}
@endsection

@section('sidebar')
    @include('guilds._sidebar')
@endsection

@section('content')
    {!! breadcrumbs([ucwords(__('guilds.guilds')) => __('guilds.guilds'), $guild->name => $guild->viewUrl, 'Members' => 'members']) !!}

    <h1>{{ $guild->name }}'s Members</h1>

    <div>
        {!! Form::open(['method' => 'GET', 'class' => 'form-inline justify-content-end']) !!}
        <div class="form-group mr-3 mb-3">
            {!! Form::text('name', Request::get('name'), ['class' => 'form-control']) !!}
        </div>
        <div class="form-group mr-3 mb-3">
            {!! Form::select(
                'sort',
                [
                    'alpha' => 'Sort Alphabetically (A-Z)',
                    'alpha-reverse' => 'Sort Alphabetically (Z-A)',
                    'reputation' => 'Sort by Reputation',
                    'newest' => 'Join Date (Newest)',
                    'oldest' => 'Join Date (Oldest) (Default)',
                ],
                Request::get('sort') ?: 'oldest',
                ['class' => 'form-control'],
            ) !!}
        </div>
        <div class="form-group mr-3 mb-3">
            {!! Form::select(
                'permissions',
                [
                    '' => 'All',
                    2 => 'Owner',
                    1 => 'Mod',
                    0 => 'Member',
                ],
                Request::get('permissions') ?: '',
                ['class' => 'form-control'],
            ) !!}
        </div>
        <div class="form-group mb-3">
            {!! Form::submit('Search', ['class' => 'btn btn-primary']) !!}
        </div>
        {!! Form::close() !!}
    </div>

    <div class="row">
        <div class="col-md col-md-12">
            @if ($guild->members)
                @include('guilds._member_table', ['members' => $members])
            @endif
        </div>
    </div>
@endsection
