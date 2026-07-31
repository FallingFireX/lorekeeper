@php
    $entries = isset($tracker)
        ? collect([$tracker])
        : ($trackers ?? collect());

    $entries = $entries->sortBy('id');
@endphp
<hr class="my-3">
<div class="row">
    @if($entries->isEmpty())
        <p>No tracker entries yet.</p>
    @else
        @foreach($entries as $entry)
            @php
                $data = $entry->data ?? [];
                // Check external_url if your application allows internal gallery ID routing here
                $gallerySubmission = $entry->external_url && is_numeric($entry->external_url) 
                    ? \App\Models\Gallery\GallerySubmission::where('id', $entry->external_url)->visible(Auth::user() ?? null)->first() 
                    : null;
            @endphp
            <div class="col-md-3 d-flex">            
                <div class="tracker-entry card mb-3 flex-fill">
                    <div class="card-body">
                        
                        <!-- Gallery link / Image resolution logic -->
                        @if($gallerySubmission)
                            @include('galleries._thumb', ['submission' => $gallerySubmission, 'gallery' => false])
                        @elseif($entry->deviant_art_image) 
                            <!-- Renders the dynamic cached image from the model -->
                            <a href="{{ $entry->external_url }}" target="_blank">
                                <img src="{{ $entry->deviant_art_image }}" alt="Character Art" class="img-fluid rounded">
                            </a>
                        @elseif($entry->external_url)
                            <!-- Fallback standard link button if the link isn't from DeviantArt -->
                            <a href="{{ $entry->external_url }}" target="_blank" class="btn btn-primary btn-sm btn-block">View External Art</a>
                        @else
                            <h4>No artwork linked</h4>
                        @endif

                        <h5 class="mt-2"> ID: {{ $entry->id }}
                            <div class="badge badge-{{ $entry->status == 'Pending' || $entry->status == 'Draft' ? 'secondary' : ($entry->status == 'Approved' ? 'success' : 'danger') }} mb-4 float-right">
                                {{ $entry->status }}
                            </div>
                        </h5>
                        <hr>
                        
                        <span class="text-muted text-capitalize">
                            {!! $entry->notes !!}
                        </span>
                    </div>
                </div>
            </div>
        @endforeach
    @endif
</div>
