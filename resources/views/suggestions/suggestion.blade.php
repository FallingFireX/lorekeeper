@extends('layouts.app')

@section('title')
    Suggestion #{{ $suggestion->id }}
@endsection


@section('content')
<div class="mb-3">
    <div class="row">
        <div class="col-md-10">
            <h1>Suggestion #{{ $suggestion->id }}: {!! $suggestion->title !!}</h1>
            <div><h3>Category: {!! $suggestion->category->name !!}</h3></div>
            <div class="mt-3">Suggested {!! pretty_date($suggestion->created_at) !!} by {!! $suggestion->user->displayName !!} </div>

            
        </div>
        <div class="col-md-2">
            <div>tags go here</div>
        </div>
    </div>
</div>
    <div class="card">
        <div class="card-header"><h5>Suggestion Details:</h5></div>
        <div class="card-body">
            <p>{!! $suggestion->text !!}</p>
        </div>
    </div>
    <div class="text-center">Like this suggestion? [votes]</div>
    
        
        
    
    
@endsection

@section('scripts')
    @parent
@endsection

