<div class="card mb-3 stock {{ $stock ? '' : 'hide' }}">
    <div class="card-body">
        <div class="text-right mb-3"><a href="#" class="remove-stock-button btn btn-danger">Remove</a></div>
        <div class="form-group">
            {!! Form::label('item_id[' . $key . ']', 'Item') !!}
            {!! Form::select('item_id[' . $key . ']', $items, $stock ? $stock->item_id : null, ['class' => 'form-control stock-field item-select selectize', 'data-name' => 'item_id', 'placeholder' => 'Select item...']) !!}
        </div>
        <div class="form-group">
            <div class="row">
                <div class="col-4">
                    {!! Form::label('cost[' . $key . ']', 'Cost') !!}
                    {!! Form::text('cost[' . $key . ']', $stock ? $stock->cost : null, ['class' => 'form-control stock-field', 'data-name' => 'cost']) !!}
                </div>
                <div class="col-4">
                    {!! Form::label('guild_cost[' . $key . ']', 'Guild Cost') !!}
                    {!! Form::text('guild_cost[' . $key . ']', $stock ? $stock->guild_cost : null, ['class' => 'form-control stock-field', 'data-name' => 'guild_cost']) !!}
                </div>
                <div class="col-4">
                    {!! Form::label('currency_id[' . $key . ']', 'Currency') !!}
                    {!! Form::select('currency_id[' . $key . ']', $currencies, $stock ? $stock->currency_id : null, ['class' => 'form-control stock-field', 'data-name' => 'currency_id']) !!}
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    {!! Form::checkbox('is_limited_stock[' . $key . ']', 1, $stock ? $stock->is_limited_stock : false, ['class' => 'form-check-input stock-limited stock-toggle stock-field', 'data-name' => 'is_limited_stock']) !!}
                    {!! Form::label('is_limited_stock[' . $key . ']', 'Set Limited Stock', ['class' => 'form-check-label ml-3']) !!} {!! add_help('If turned on, will limit the amount purchaseable to the quantity set below.') !!}
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    {!! Form::checkbox('guild_only[' . $key . ']', 1, $stock ? $stock->guild_only : false, ['class' => 'form-check-input stock-limited stock-toggle stock-field', 'data-name' => 'guild_only']) !!}
                    {!! Form::label('guild_only[' . $key . ']', 'Guild Only', ['class' => 'form-check-label ml-3']) !!} {!! add_help('If turned on, only members of the guild can purchase this item.') !!}
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group qty-wrapper">
                    {!! Form::label('quantity[' . $key . ']', 'Quantity') !!} {!! add_help('At minimum, 1 unit will be transferred to the shop. All items will come from the guild inventory.') !!}
                    {!! Form::number('quantity[' . $key . ']', $stock ? $stock->quantity : 1, ['class' => 'form-control stock-field quantity', 'min' => 1, 'data-name' => 'quantity']) !!}
                    <small class="{{ $stock ?? 'hide' }}">Currently in Inventory: {{ isset($itemCounts) && isset($itemCounts[$stock->item_id]) ? $itemCounts[$stock->item_id] : 0 }}</small>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    {!! Form::label('purchase_limit[' . $key . ']', 'User Purchase Limit') !!} {!! add_help('This is the maximum amount of this item a user can purchase from this shop. Set to 0 to allow infinite purchases.') !!}
                    {!! Form::text('purchase_limit[' . $key . ']', $stock ? $stock->purchase_limit : 0, ['class' => 'form-control stock-field', 'data-name' => 'purchase_limit']) !!}
                </div>
            </div>
        </div>
    </div>
</div>
