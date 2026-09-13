@extends('layouts.app')

@section('title')
    {{ ucwords(__('guilds.guilds')) }}
@endsection

@section('content')
    {!! breadcrumbs([ucwords(__('guilds.guilds')) => __('guilds.guilds')]) !!}

    <h1>{{ ucwords(__('guilds.guilds')) }}</h1>

    <p>This is a list of all {{ ucwords(__('guilds.guilds')) }} on the site, including disbanded guilds. If you want to form a {{ ucwords(__('guilds.guild')) }}, you can form a new <a href="#">{{ ucwords(__('guilds.guild')) }} here</a>.</p>

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
                    'newest' => 'Newest First (Default)',
                    'oldest' => 'Oldest First',
                ],
                Request::get('sort') ?: 'newest',
                ['class' => 'form-control'],
            ) !!}
        </div>
        <div class="form-group mb-3">
            {!! Form::submit('Search', ['class' => 'btn btn-primary']) !!}
        </div>
        {!! Form::close() !!}
    </div>

    <div class="row">
        {!! $guilds->render() !!}
        @foreach ($guilds->chunk(1) as $chunk)
            @foreach ($chunk as $guild)
                <div class="col-md-12">
                    @include('guilds._guild_box', ['guild' => $guild])
                </div>
            @endforeach
        @endforeach
        {!! $guilds->render() !!}
    </div>

    <div class="text-center mt-4 small text-muted">{{ $guilds->total() }} result{{ $guilds->total() == 1 ? '' : 's' }} found.</div>
@endsection
