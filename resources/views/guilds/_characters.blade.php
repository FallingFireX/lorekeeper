<?php $i = 0; ?>
<div class="row">
    @foreach ($characters as $gc)
        <?php $character = $gc->character; ?>
        <div class="col-md-4 col-6 mb-2">
            <div class="text-center">
                <a href="{{ $character->url }}"><img src="{{ $character->image->thumbnailUrl }}" class="img-thumbnail" alt="Thumbnail for {{ $character->fullName }}" /></a>
            </div>
            <div class="mt-2">
                <a href="{{ $character->url }}" class="h5 mb-0">
                    @if (!$character->is_visible)
                        <i class="fas fa-eye-slash"></i>
                    @endif {{ Illuminate\Support\Str::limit($character->fullName, 20, $end = '...') }}
                </a>
            </div>
            <div class="d-flex align-items-center justify-content-between">
                {!! $character->displayOwner !!}
                @if ($gc->rank)
                    {!! $gc->rank->displayName !!}
                @endif
            </div>
        </div>
        @if (isset($limit) && $i === $limit - 1)
        @break
    @endif
@endforeach
</div>
