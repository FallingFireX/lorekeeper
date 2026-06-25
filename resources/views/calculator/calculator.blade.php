@extends('layouts.app')

@section('title')
    Calc
@endsection

<script>
    window.EDIT_SUBMISSION = @json($submission ?? null);
</script>


@section('content')
<div id="flash-badge" class="alert d-none" role="alert"></div>
@if(request()->is('fp/calculator'))
    <h5>
        Submit artwork for review
</h5>
@endif

@if(request()->is('fp/art-submission/*/edit'))
    <h5>You are editing submission #{{ $submission->id }}.</h5>
@endif

<div class="container mt-4">
    <div class="card mb-4">
        <div class="card-body">
            <h3>Basic info</h3>
            <div class="row">
                @if(request()->is('dp/art-submission/*/edit'))
                    <div class="col-md-6">
                        {!! Form::label('character_id', 'Selected Character (only change this if you messed up your selection!)') !!}
                        {!! Form::select('character_id', $characters, $submission->character_id, [
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
                    {!! Form::label('Select Gallery Submission') !!}
                    {!! Form::select('url', $user_galleries, null, ['class' => 'form-control mr-2 gallerySelect', 
                    'placeholder' => 'Select a Gallery...',
                    ]) !!}
                    <div class="my-1"> Or enter a URL</div>
                    {!! Form::url('external_url', null, ['class' => 'form-control mb-4', 'id' => 'external_url', 'placeholder' => 'Enter a URL (deviantArt only)!']) !!}
                </div>
            </div>
            <div class="my-1">Points breakdown</div>
            {!! Form::textarea('notes', null, ['class' => 'form-control mb-4', 'id' => 'notes', 'placeholder' => 'This is an optional space to give us extra information about your submission']) !!}
        
        </div>
    </div>
</div>

<form id="artForm" class="container mt-4">
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h3>Points</h3>
            <hr class="my-3">
            <!-- Art Type Dropdown -->
             <h4 class="pb-2">Artworks</h4>
            <h6 class="mb-4">
                <i class="mt-2">Select Type of Artwork</i>
                <br>
                <select id="art_type" class="form-select form-select-lg btn btn-secondary dropdown-toggle px-3 mt-2">
                    <option value="none" data-multiplier="0">None</option>
                    <option value="headshot" data-multiplier="1">Headshot</option>
                    <option value="partial" data-multiplier="2">Partial</option>
                    <option value="fullbody" data-multiplier="3">Fullbody</option>
                </select>
            </h6>
            <p id="art_description" class="mt-3">Please select an art type.</p>

            <div class="row">
                <!-- Tags -->
                <div class="col-md-6 mb-3">
                    <h6 class="fw-bold border-bottom pb-2">Base Points</h6>
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input tags" id="tagBase" data-base="2">
                        <label class="form-check-label" for="tagBase">Base</label>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input tags" id="tagColored" data-base="3">
                        <label class="form-check-label" for="tagColored">Colored</label>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input tags" id="tagShaded" data-base="2">
                        <label class="form-check-label" for="tagShaded">Shaded</label>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input tags" id="tagBg" data-base="3">
                        <label class="form-check-label" for="tagBg">Background</label>
                    </div>
                </div>

                <!-- Bonuses Section -->
                <div class="col-md-6 mb-3">
                    <h6 class="fw-bold border-bottom pb-2">Bonuses</h6>
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input bonus" id="bonusPers" value="2">
                        <label class="form-check-label" for="bonusPers">Personal</label>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input bonus" id="bonusCollab" value="1">
                        <label class="form-check-label" for="bonusCollab">Collab</label>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input bonus" id="bonusVanshi" value="2">
                        <label class="form-check-label" for="bonusVanshi">Other Vanshi</label>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input bonus" id="bonusAlien" value="2">
                        <label class="form-check-label" for="bonusAlien">Alien Creature</label>
                    </div>
                </div>
            </div>
            <hr class="my-3">
            <h4>Writing</h4>
                <div class="form-check mt-3">
                    <input type="number" class="form-control lit" id="lit_count" placeholder="0">
                    <label class="form-check-label" for="lit_count">Enter word count (2 pts per 100 words)</label>
                </div>
        </div>

        <!-- Footer with Total and Submit -->
        <div class="card-footer d-flex justify-content-between align-items-center bg-dark">
            <h4 class="mb-0">Total: <span id="total" class="badge bg-secondary">0</span></h4>
            @if(request()->is('dp/art-submission/*/edit'))
                <button type="submit" class="btn btn-success btn-lg px-5">Edit Submission</button>
            @else
                <button type="submit" class="btn btn-success btn-lg px-5">Submit DP For Review</button>
            @endif
        </div>
    </div>

             
</form>
</form>

@endsection

@section('scripts')
    @include('js.calculator_js')

    <script>
        // Get the dropdown and the elements to update
    const artTypeSelect = document.getElementById('art_type');
    const artDescription = document.getElementById('art_description');
    const artMultiplier = document.getElementById('art_multiplier');

    // Define the text descriptions for each value (or use data attributes)
    const descriptions = {
        'none': 'No art selected: This is perfect for if you are submitting only literature. If you have written work and visual, select the correct type AND input the word count below!.',
        'headshot': 'Headshot: Normally focuses on the head and neck, but this is anything including up to 25% of the body',
        'partial': 'Partial: This is roughly 50% of the body, being the head or tail end.',
        'fullbody': 'Fullbody: Should show at least 75% of the total body, this is required for activities and events!'
    };

    // Add an event listener for when the dropdown selection changes
    artTypeSelect.addEventListener('change', function() {
        // Get the selected value
        const selectedValue = this.value; // 'headshot', 'partial', etc.

        // Get the selected option element
        const selectedOption = this.options[this.selectedIndex];

        // Update the description text
        artDescription.textContent = descriptions[selectedValue] || 'Invalid selection.';

    });

     artTypeSelect.dispatchEvent(new Event('change'));
</script>
    
@endsection
