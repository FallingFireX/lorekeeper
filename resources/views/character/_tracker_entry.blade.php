@extends('admin.layout')

@section('admin-title')
    FP Submission (#{{ $submission->id }})
@endsection

@php
$gallerySubmission = $submission->url ? \App\Models\Gallery\GallerySubmission::where('id', $submission->url)->visible(Auth::user() ?? null)->first() : null;
@endphp

@section('admin-content')
    {!! breadcrumbs(['Admin Panel' => 'admin', 'FP Submissions Queue' => 'admin/fp-submissions/pending' , 'FP Submission #' => 'admin/fp-submission/#/edit']) !!}
        <div class="row">
            <div class="col-md-6">
                <div class="tracker-entry card my-3">
                    <div class="card-body">
                        <h2>Tracker ID: #{{ $submission->id }}</h2>
                        <h5>Submitted by: {!! $submission->user->displayName !!}</h5>
                        @if($gallerySubmission)
                            @include('galleries._thumb', ['submission' => $gallerySubmission, 'gallery' => false])
                        @else
                            <div class="img-thumbnail d-flex align-items-center justify-content-center" style="height:{{ config('lorekeeper.settings.masterlist_thumbnails.height') }}px;">
                                <span class="text-muted">No image linked</span>
                            </div>
                        @endif

                        <h5 class="my-3">Character: {!! $character->displayName !!}</h5>
                        {{-- Art Type --}}
                        <h5 class="mb-2">
                            Art Type:
                            <span class="text-muted text-capitalize">
                                {{ $submission->data['art_type'] ?? 'Unknown' }}
                            </span>
                        </h5>

                        {{-- Tags --}}
                        @if(!empty($submission->data['tags']))
                            <div class="mb-2">
                                <strong>Tags</strong>
                                <ul class="mb-0">
                                    @foreach($submission->data['tags'] as $tag)
                                        <li>
                                            {{ $tag['name'] ?? 'Tag' }}:
                                            <strong>+{{ $tag['applied_value'] ?? 0 }}</strong>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        {{-- Bonuses --}}
                        @if(!empty($submission->data['bonuses']))
                            <div class="mb-2">
                                <strong>Bonuses</strong>
                                <ul class="mb-0">
                                    @foreach($submission->data['bonuses'] as $bonus)
                                        <li>
                                            {{ $bonus['name'] ?? 'Bonus' }}
                                            <strong>
                                                +{{ $bonus['value'] ?? 0 }}
                                            </strong>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        {{-- Literature --}}
                        @if(!empty($submission->data['literature']))
                            <div class="mb-2">
                                <strong>Literature</strong>
                                <div>
                                    Word Count:
                                    {{ number_format($submission->data['literature']['word_count'] ?? 0) }}
                                </div>
                                <div>
                                    Points:
                                    <strong>{{ $submission->data['literature']['points'] ?? 0 }}</strong>
                                </div>
                            </div>
                        @endif


                        {{-- Total --}}
                        <hr>
                        <div class="text-end">
                            <strong>Total Points:</strong>
                            <span class="fs-5">
                                {{ $submission->data['total'] ?? 0 }}
                            </span>
                        </div>

                    </div>
                </div>
            </div>
            <div class="col-md-6 text-center">
                <h1>
                    FP Submission (# {!! $submission->id !!})
                </h1>
                <h1>
                    <div class="badge badge-{{ $submission->status == 'Pending' || $submission->status == 'Draft' ? 'secondary' : ($submission->status == 'Approved' ? 'success' : 'danger') }} mb-4">
                        {{ $submission->status }}
                    </div>
                </h1>
                @if ($submission->status == 'Pending')
                    <p class="mt-4"><i>This FP submission is still pending approval. You may still edit it if youve forgotten something.</i></p> 
                    <h2><a class="btn btn-secondary" href="{{ $submission->viewUrl }}">Edit Submission</a></h2>
                @elseif ($submission->status == 'Approved')
                    This FP submission has been approved and all points awarded to your Vanshi
                @endif
            </div>
            
        </div>
        

@endsection

@section('scripts')
    @parent
    
@endsection


