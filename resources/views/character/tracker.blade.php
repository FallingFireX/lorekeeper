@extends('character.layout', ['isMyo' => $character->is_myo_slot])

@section('profile-title')
    {{ $character->fullName }}'s Datapoint Tracker
@endsection


@section('profile-content')
    {!! breadcrumbs([
        $character->category->masterlist_sub_id ? $character->category->sublist->name . ' Masterlist' : 'Character masterlist' => $character->category->masterlist_sub_id ? 'sublist/' . $character->category->sublist->key : 'masterlist',
        $character->fullName => $character->url,
        'Tracker' => $character->url . '/tracker',
    ]) !!}

    @include('character._header', ['character' => $character])

    <div class="text-center">
        <h1>{{ $character->fullName }}'s FP Tracker</h1>
    </div>
   <div class="text-center mb-3">
        <h5>Total Approved FP:{{ $character->total_fp }}</h5> 
        <h5>Current Rank: {{ $character->rank }}</h5> 
    </div>
    <hr>

    {!! $character->profile->tracker !!}

    @if (Auth::check() && Auth::user()->hasPower('edit_inventories'))
        <a href="#" class=" btn btn-outline-info btn-sm" id="grantButton" data-toggle="modal" data-target="#grantModal"><i class="fas fa-cog"></i> Admin (Grant DP)</a>
    @endif
    @if (Auth::check() && Auth::user()->hasPower('edit_inventories'))
            <div class="modal fade" id="grantModal" tabindex="-1" role="dialog">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <span class="modal-title h5 mb-0">[ADMIN] Grant Items</span>
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                        </div>
                        <div class="modal-body">
                            <p>Note that granting items does not check against any category hold limits for characters.</p>
                            <div class="form-group">
                                {!! Form::open(['url' => 'admin/character/' . $character->slug . '/grant-dp']) !!}

                                {!! Form::label('Item(s)') !!} {!! add_help('Must have at least 1 item and Quantity must be at least 1.') !!}
                                    <div class="d-flex mb-2">
                                        {!! Form::text('quantity', 1, ['class' => 'form-control mr-2', 'placeholder' => 'Amount']) !!}
                                    <a href="#" class="remove-item btn btn-danger mb-2 disabled">×</a>
                                    </div>

                                <h5>Additional Data</h5>

                                <div class="form-group">
                                    {!! Form::label('data', 'Reason') !!} {!! add_help('Additional notes for the item. This will appear in the item\'s description, but not in the logs.') !!}
                                    {!! Form::text('data', null, ['class' => 'form-control', 'maxlength' => 400]) !!}
                                </div>

                                <div class="text-right">
                                    {!! Form::submit('Submit', ['class' => 'btn btn-primary']) !!}
                                </div>

                                {!! Form::close() !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    
   @include('character._tab_tracker')
@endsection
@section('scripts')
    
@endsection
