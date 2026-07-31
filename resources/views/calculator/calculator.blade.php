@extends('layouts.app')

@section('title')
    Calc
@endsection


@section('content')

{{-- 1. Open the form correctly at the very top using Laravel Collective --}}
@if(isset($submission))
    {!! Form::model($submission, ['url' => 'fp/calculator/edit/'.$submission->id, 'method' => 'POST', 'id' => 'artForm', 'class' => 'container mt-4']) !!}
@else
    {!! Form::open(['url' => 'fp/calculator/create', 'method' => 'POST', 'id' => 'artForm', 'class' => 'container mt-4']) !!}
@endif

@if(request()->is('fp/calculator'))
    <h5>Submit artwork for review</h5>
@endif

@if(request()->is('fp/calculator/edit/*'))
    <h5>You are editing submission #{{ $tracker->id }}.</h5>
@endif

<div class="card mb-4">
    <div class="card-body">
        <h3>Basic info</h3>
        <div class="row">
            @if(request()->is('fp/calculator/edit/*'))
                <div class="col-md-6">
                    {!! Form::label('character_id', 'Selected Character (only change this if you messed up your selection!)') !!}
                    {!! Form::select('character_id', $characters, $tracker->character_id, [
                        'class' => 'form-control mr-2 characterSelect', 
                        'placeholder' => 'Select a Character...',
                        'required' => 'required' 
                    ]) !!}
                </div>
            @else
            <div class="col-md-6">
                {!! Form::label('character_id', 'Select Character') !!}
                {!! Form::select('character_id', $users_character, null, [
                    'class' => 'form-control mr-2 characterSelect', 
                    'placeholder' => 'Select a Character...',
                    'required' => 'required' 
                ]) !!}
            </div>
            @endif
            <div class="col-md-6">
                <div class="my-1"> Enter a URL</div>
                {!! Form::url('external_url', null, ['class' => 'form-control mb-4', 'id' => 'external_url', 'placeholder' => 'Enter a URL (deviantArt only)!']) !!}
            </div>
        </div>
        <div class="my-1">Points breakdown</div>
        {!! Form::textarea('notes', $tracker->notes, ['class' => 'form-control wysiwyg']) !!}
    
    </div>
</div>

<div class="card shadow-sm mb-4">
    <div class="card-body">
        {!! Form::number('total', null, ['class' => 'form-control total', 'id' => 'total', 'placeholder' => '0']) !!}
        <label class="form-check-label" for="total">Enter FP total</label>
    </div>

    <div class="card-footer d-flex justify-content-between align-items-center bg-dark">
        <h4 class="mb-0 text-white">You are submitting: <span id="total-display" class="badge bg-secondary">0</span> FP</h4>
        <i>Be sure your calculations are correct!</i>
        @if(request()->is('fp/calculator/edit/*'))
            <button type="submit" class="btn btn-success btn-lg px-5">Edit Submission</button>
        @else
            <button type="submit" class="btn btn-success btn-lg px-5">Submit FP For Review</button>
        @endif
    </div>
</div>

{!! Form::close() !!}

@endsection

@section('scripts')
@include('js._tinymce_wysiwyg')

<script>
    window.EDIT_SUBMISSION = @json($submission ?? null);
    document.addEventListener('DOMContentLoaded', () => {
    const totalInput = document.querySelector('.form-control.total');
    const displaySpan = document.getElementById('total-display');

    const updateDisplay = () => {
        const value = totalInput.value || '0';
        displaySpan.textContent = value;
    };

    updateDisplay();

    totalInput.addEventListener('input', updateDisplay);
});

</script>
    
@endsection