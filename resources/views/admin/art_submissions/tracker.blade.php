@extends('admin.layout')

@section('admin-title')
    DP Submission (#{{ $submission->id }})
@endsection

@php
$gallerySubmission = $submission->url ? \App\Models\Gallery\GallerySubmission::where('id', $submission->url)->visible(Auth::user() ?? null)->first() : null;
@endphp

@section('admin-content')
    {!! breadcrumbs(['Admin Panel' => 'admin', 'DP Submissions Queue' => 'admin/dp-submissions/pending' , 'DP Submission #' => 'admin/dp-submission/#/edit']) !!}
    
        <h1>
            DP Submission (# {!! $submission->id !!})
            <span class="float-right badge badge-{{ $submission->status == 'Pending' || $submission->status == 'Draft' ? 'secondary' : ($submission->status == 'Approved' ? 'success' : 'danger') }}">
                {{ $submission->status }}
            </span>
        </h1>

        <div class="row">
            <div class="col-md-6">
                <div class="tracker-entry card my-3">
                    <div class="card-body">
                        <h2>Tracker ID: #{{ $submission->id }}</h2>
                        <h5>Submitted by: {!! $submission->user->displayName !!}</h5>
                        @if($gallerySubmission)
                            @include('galleries._thumb', ['submission' => $gallerySubmission, 'gallery' => false])
                        @elseif($submission->deviant_art_image) 
                            <!-- Renders the dynamic cached image from the model -->
                            <a href="{{ $submission->external_url }}" target="_blank">
                                <img src="{{ $submission->deviant_art_image }}" alt="Character Art" class="img-fluid rounded">
                            </a>
                        @elseif($submission->external_url)
                            <!-- Fallback standard link button if the link isn't from DeviantArt -->
                            <a href="{{ $submission->external_url }}" target="_blank" class="btn btn-primary btn-sm btn-block">View External Art</a>
                        @else
                            <h4>No artwork linked</h4>
                        @endif

                        <h5 class="my-3">Character: {!! $character->displayName !!}</h5>
                        @if($submission->notes)
                        <div class="card p-2 my-2">
                            <h3>Breakdown and Notes:</h3>
                            <hr class="my-2">

                            {!! $submission->notes !!}
                        </div>
                        @endif


                        {{-- Total --}}
                        <hr>
                        <div class="text-end">
                            <strong>Total Points:</strong>
                            <span class="fs-5">
                                {{ $submission->total ?? 0 }}
                            </span>
                        </div>

                    </div>
                </div>
                
            </div>
            <div class="col-md-6 mt-4">
                @if($submission->status == 'Approved' || $submission->status == 'Rejected')
                    <h2>Staff Comments ({!! $submission->staff->displayName !!})</h2>
                    <div class="card mb-3">
                        <div class="card-body">
                            @if (isset($submission->parsed_staff_comments))
                                {!! $submission->parsed_staff_comments !!}
                            @else
                                {!! $submission->staff_comments !!}
                            @endif
                        </div>
                    </div>
                    @endif
                
                
                @if ($submission->status == 'Pending')
                    {!! Form::open(['url' => url()->current(), 'id' => 'submissionForm']) !!}

                    <div class="alert alert-danger">
                        Approving a Tracker submission will make it non editable; make sure you double check all bonuses and points!
                        Rejecting a submission will null it entirely, and remove it from the player's tracker. Make sure you tell the player why its rejected!
                    </div>
                    <div class="form-group">
                        {!! Form::label('staff_comments', 'Staff Comments (Optional)') !!}
                        {!! Form::textarea('staff_comments', $submission->staff_comments, ['class' => 'form-control wysiwyg']) !!}
                    </div>

                            <div class="align-bottom text-center">
                                <a href="#" class="btn btn-danger mr-2 px-5" id="rejectionButton">Reject</a>
                                <a href="#" class="btn btn-secondary mr-2 px-5" id="cancelButton">Cancel</a>
                                <a href="#" class="btn btn-success px-5" id="approvalButton">Approve</a>
                            </div>

                            {!! Form::close() !!}

                            <div class="modal fade" id="confirmationModal" tabindex="-1" role="dialog">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content hide" id="approvalContent">
                                        <div class="modal-header">
                                            <span class="modal-title h5 mb-0">Confirm Approval</span>
                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                        </div>
                                        <div class="modal-body">
                                            <p>This will approve the DP and distribute the above rewards to the user.</p>
                                            <div class="text-right">
                                                <a href="#" id="approvalSubmit" class="btn btn-success">Approve</a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-content hide" id="cancelContent">
                                        <div class="modal-header">
                                            <span class="modal-title h5 mb-0">Confirm Cancellation</span>
                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                        </div>
                                        <div class="modal-body">
                                            <p>
                                                This will cancel the DP and send it back to drafts.
                                                Make sure to include a staff comment if you do this!
                                            </p>
                                            
                                            <div class="text-right">
                                                <a href="#" id="cancelSubmit" class="btn btn-secondary">Cancel</a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-content hide" id="rejectionContent">
                                        <div class="modal-header">
                                            <span class="modal-title h5 mb-0">Confirm Rejection</span>
                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                        </div>
                                        <div class="modal-body">
                                            <p>This will reject the DP completely.</p>
                                            <div class="text-right">
                                                <a href="#" id="rejectionSubmit" class="btn btn-danger">Reject</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="alert alert-danger">This DP Submission has already been processed.</div>
                        @endif
            </div>
        </div>
        

@endsection

@section('scripts')
    @parent
    @if ($submission->status == 'Pending')
        @include('js._tinymce_wysiwyg')
        <script>
            $(document).ready(function() {
                var $confirmationModal = $('#confirmationModal');
                var $submissionForm = $('#submissionForm');

                var $approvalButton = $('#approvalButton');
                var $approvalContent = $('#approvalContent');
                var $approvalSubmit = $('#approvalSubmit');

                var $rejectionButton = $('#rejectionButton');
                var $rejectionContent = $('#rejectionContent');
                var $rejectionSubmit = $('#rejectionSubmit');

                var $cancelButton = $('#cancelButton');
                var $cancelContent = $('#cancelContent');
                var $cancelSubmit = $('#cancelSubmit');

                $approvalButton.on('click', function(e) {
                    e.preventDefault();
                    $approvalContent.removeClass('hide');
                    $rejectionContent.addClass('hide');
                    $cancelContent.addClass('hide');
                    $confirmationModal.modal('show');
                });

                $rejectionButton.on('click', function(e) {
                    e.preventDefault();
                    $rejectionContent.removeClass('hide');
                    $approvalContent.addClass('hide');
                    $cancelContent.addClass('hide');
                    $confirmationModal.modal('show');
                });

                $cancelButton.on('click', function(e) {
                    e.preventDefault();
                    $cancelContent.removeClass('hide');
                    $rejectionContent.addClass('hide');
                    $approvalContent.addClass('hide');
                    $confirmationModal.modal('show');
                });

                $approvalSubmit.on('click', function(e) {
                    e.preventDefault();
                    $submissionForm.attr('action', '{{ url()->current() }}/approve');
                    $submissionForm.submit();
                });

                $rejectionSubmit.on('click', function(e) {
                    e.preventDefault();
                    $submissionForm.attr('action', '{{ url()->current() }}/reject');
                    $submissionForm.submit();
                });

                $cancelSubmit.on('click', function(e) {
                    e.preventDefault();
                    $submissionForm.attr('action', '{{ url()->current() }}/cancel');
                    $submissionForm.submit();
                });
            });
        </script>
    @endif
@endsection


