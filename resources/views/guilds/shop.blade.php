@extends('layouts.app')

@section('title')
    {{ ucwords(__('guilds.guilds')) }}
@endsection

@section('sidebar')
    @include('guilds._sidebar')
@endsection

@section('content')
    <x-admin-edit title="Shop" :object="$shop" />
    {!! breadcrumbs([ucwords(__('guilds.guilds')) => __('guilds.guilds'), $guild->name => $guild->name]) !!}

    @if ($shop->name)
        <h4 class="text-muted">{{ $guild->name }}'s Shop</h4>
    @endif
    <h1>{{ $shop->name ?? $guild->name . '\'s Shop' }}</h1>

    <div class="text-center">
        @if ($shop->has_image)
            <img src="{{ $shop->shopImageUrl }}" style="max-width:100%" alt="{{ $shop->name }}" />
        @endif
        <p>{!! $shop->parsed_description !!}</p>
    </div>

    <!-- <pre style="background:#ccc">
        {{ print_r($categories, true) }}
    </pre>  -->

    @foreach ($items as $categoryId => $categoryItems)
        <?php
        $visible = '';
        if ($categoryId && !$categories[$categoryId]->is_visible) {
            $visible = '<i class="fas fa-eye-slash mr-1"></i>';
        }
        ?>
        <div class="card mb-3 inventory-category">
            <h5 class="card-header inventory-header">
                {!! isset($categories[$categoryId]) ? '<a href="' . $categories[$categoryId]->searchUrl . '">' . $visible . $categories[$categoryId]->name . '</a>' : 'Miscellaneous' !!}
            </h5>
            <div class="card-body inventory-body">
                @foreach ($categoryItems->chunk(4) as $chunk)
                    <div class="row mb-3">
                        @foreach ($chunk as $item)
                            <div class="col-sm-3 col-6 text-center inventory-item" data-id="{{ $item->pivot->id }}">
                                <div class="mb-1">
                                    <a href="#" class="inventory-stack">
                                        <img src="{{ $item->imageUrl }}" alt="{{ $item->name }}" />
                                    </a>
                                </div>
                                <div>
                                    <a href="#" class="inventory-stack inventory-stack-name">
                                        <strong>{{ $item->name }}</strong>
                                    </a>
                                    <div>
                                        <strong>Cost: </strong> {!! $currencies[$item->pivot->currency_id]->display($item->pivot->cost) !!}
                                    </div>
                                    @if ($item->pivot->is_limited_stock)
                                        <div>
                                            Stock: {{ $item->pivot->quantity }}
                                        </div>
                                    @endif
                                    @if ($item->pivot->purchase_limit)
                                        <div class="text-danger">
                                            Max {{ $item->pivot->purchase_limit }} per user
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endforeach
            </div>
        </div>
    @endforeach
@endsection
@section('scripts')
    <script>
        $(document).ready(function() {
            $('.inventory-item').on('click', function(e) {
                e.preventDefault();
                console.log("{{ url('guilds/shops/' . $shop->id) }}/" + $(this).data('id'));
                loadModal("{{ url('guilds/shops/' . $shop->id) }}/" + $(this).data('id'), 'Purchase Item');
            });
        });
    </script>
@endsection
