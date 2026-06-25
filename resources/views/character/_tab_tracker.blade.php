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
                 $gallerySubmission = $entry->url ? \App\Models\Gallery\GallerySubmission::where('id', $entry->url)->visible(Auth::user() ?? null)->first() : null;
            @endphp
            <div class="col-md-3 d-flex">            
            <div class="tracker-entry card mb-3 flex-fill">
                <div class="card-body">
                    <!-- Gallery link -->
                    @if($gallerySubmission)
                        @include('galleries._thumb', ['submission' => $gallerySubmission, 'gallery' => false])
                    @elseif($entry->external_url)
                        <h4><a href= "{{ $entry->external_url }}">Link</a></h4>
                    @else
                        <h4>No artwork linked</h4>
                    @endif
                    <h5 class="mt-2"> ID: {{ $entry->id }}
                    <div class="badge badge-{{ $entry->status == 'Pending' || $entry->status == 'Draft' ? 'secondary' : ($entry->status == 'Approved' ? 'success' : 'danger') }} mb-4 float-right">
                        {{ $entry->status }}
                    </div>
                    </h5>
                    <hr>
                    <!-- Art details -->
                    
                </div>
            </div>
            </div>
            @endforeach
        @endif
        
</div>

    

