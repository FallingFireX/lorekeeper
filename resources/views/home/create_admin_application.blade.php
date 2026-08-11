@extends('layouts.app')

@section('title', 'Submit Application')

@section('content')
    <h1>Submit an Application</h1>

    {!! Form::open(['url' => route('applications.store'), 'method' => 'POST']) !!}
    @csrf

    {{-- Team select --}}
    <div class="form-group">
        {!! Form::label('team_id', 'Select Team') !!}
        {!! Form::select('team_id', $teams->pluck('name', 'id'), request('team_id'), ['class' => 'form-control', 'required']) !!}
    </div>

    {{-- Application text --}}
    <div class="form-group">
        {!! Form::label('application', 'Your Application') !!}
        {!! Form::textarea('application', null, ['class' => 'form-control wysiwyg']) !!}
    </div>

    <button type="submit" class="btn btn-primary">Submit</button>
    {!! Form::close() !!}
@endsection
