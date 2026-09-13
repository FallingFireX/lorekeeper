@extends('layouts.app')

@section('title')
    {{ ucwords(__('guilds.guilds')) }}
@endsection

@section('sidebar')
    @include('guilds._sidebar')
@endsection

@section('content')
    {!! breadcrumbs([ucwords(__('guilds.guilds')) => __('guilds.guilds'), $guild->name => $guild->viewUrl . '/shop', 'Create Shop' => 'create']) !!}

    <h1>{{ $shop && $shop->id ? 'Edit' : 'Create' }} Shop
        @if ($shop && $shop->id)
            ({!! $shop->displayName !!})
        @endif
    </h1>

    {!! Form::open(['url' => __('guilds.guilds') . '/' . $guild->id . '/shop/' . ($shop ? 'edit' : 'create'), 'files' => true]) !!}

    <h3>Basic Information</h3>

    <div class="form-group">
        {!! Form::label('Name') !!}
        {!! Form::text('name', $shop ? $shop->name : null, ['class' => 'form-control']) !!}
    </div>

    <div class="form-group">
        {!! Form::label('Shop Image (Optional)') !!} {!! add_help('This image is used on the shop index and on the shop page as a header.') !!}
        <div class="custom-file">
            {!! Form::label('image', 'Choose file...', ['class' => 'custom-file-label']) !!}
            {!! Form::file('image', ['class' => 'custom-file-input']) !!}
        </div>
        <div class="text-muted">Recommended size: None (Choose a standard size for all shop images)</div>
        @if ($shop && $shop->has_image)
            <div class="form-check">
                {!! Form::checkbox('remove_image', 1, false, ['class' => 'form-check-input']) !!}
                {!! Form::label('remove_image', 'Remove current image', ['class' => 'form-check-label']) !!}
            </div>
        @endif
    </div>

    <div class="form-group">
        {!! Form::label('Description (Optional)') !!}
        {!! Form::textarea('description', $shop ? $shop->description : null, ['class' => 'form-control wysiwyg']) !!}
    </div>

    <div class="form-group">
        {!! Form::checkbox('is_active', 1, $shop && $shop->id ? $shop->is_active : 1, ['class' => 'form-check-input', 'data-toggle' => 'toggle']) !!}
        {!! Form::label('is_active', 'Set Active', ['class' => 'form-check-label ml-3']) !!} {!! add_help('If turned off, the shop will not be visible to regular users.') !!}
    </div>

    <div class="text-right">
        {!! Form::submit($shop && $shop->id ? 'Edit' : 'Create', ['class' => 'btn btn-primary']) !!}
    </div>

    {!! Form::close() !!}

    @if ($shop && $shop->id)
        <div class="card">
            <h3 class="card-header">Shop Stock</h3>
            <div class="card-body">
                {!! Form::open(['url' => '/' . __('guilds.guilds') . '/' . $guild->id . '/shop/edit/stock/']) !!}
                <div class="text-right mb-3">
                    <a href="#" class="add-stock-button btn btn-outline-primary">Add Stock</a>
                </div>
                <div id="shopStock">
                    @foreach ($shop->stock as $key => $stock)
                        @include('guilds._stock', ['stock' => $stock, 'key' => $key, 'itemCounts' => $item_maxes])
                    @endforeach
                </div>
                <div class="text-right">
                    {!! Form::submit('Edit', ['class' => 'btn btn-primary']) !!}
                </div>
                {!! Form::close() !!}
                <div class="" id="shopStockData">
                    @include('guilds._stock', ['stock' => null, 'key' => 0])
                </div>
            </div>
        </div>
    @endif

@endsection
@section('scripts')
    <script>
        $(document).ready(function() {
            var $shopStock = $('#shopStock');
            var $stock = $('#shopStockData').find('.stock');
            var itemCounts = @json($item_maxes);

            $('#shopStock .selectize').selectize();

            $('.delete-shop-button').on('click', function(e) {
                e.preventDefault();
                loadModal("{{ url('admin/data/shops/delete') }}/{{ $shop->id ?? null }}", 'Delete Shop');
            });
            $('.add-stock-button').on('click', function(e) {
                e.preventDefault();

                var clone = $stock.clone();
                $shopStock.append(clone);
                clone.removeClass('hide');
                attachStockListeners(clone);
                refreshStockFieldNames();

                clone.find('.selectize').selectize();
            });
            $('#shopStock').on('change', '.item-select', function() {
                console.log('item changed!');
                var item_id = $(this).val();
                var $container = $(this).closest('.stock');

                var max = itemCounts[item_id] ?? 1;

                $container.find('.quantity').attr('max', max);
                $container.find('.qty-wrapper small').text('Currently in Inventory: ' + max).removeClass('hide');
            });

            attachStockListeners($('#shopStock .stock'));

            function attachStockListeners(stock) {
                stock.find('.stock-toggle').bootstrapToggle();
                stock.find('.stock-limited').on('change', function(e) {
                    var $this = $(this);
                    if ($this.is(':checked')) {
                        $this.parent().parent().parent().parent().find('.stock-limited-quantity').removeClass('hide');
                    } else {
                        $this.parent().parent().parent().parent().find('.stock-limited-quantity').addClass('hide');
                    }
                });
                stock.find('.remove-stock-button').on('click', function(e) {
                    e.preventDefault();
                    $(this).parent().parent().parent().remove();
                    refreshStockFieldNames();
                });
                stock.find('.card-body [data-toggle=tooltip]').tooltip({
                    html: true
                });
            }

            function refreshStockFieldNames() {
                $('.stock').each(function(index) {
                    var $this = $(this);
                    var key = index;
                    $this.find('.stock-field').each(function() {
                        $(this).attr('name', $(this).data('name') + '[' + key + ']');
                    });
                });
            }
        });
    </script>
@endsection
