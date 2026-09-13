<ul>
    <li class="sidebar-header"><a href="{{ $guild->viewUrl }}" class="card-link">{{ $guild->name }}</a></li>
    <li class="sidebar-section">
        <div class="sidebar-section-header">Storage</div>
        <div class="sidebar-item"><a href="{{ $guild->viewUrl . '/inventory' }}" class="{{ set_active('*inventory') }}">Inventory</a></div>
        <div class="sidebar-item"><a href="{{ $guild->viewUrl . '/bank' }}" class="{{ set_active('*bank') }}">Bank</a></div>
        @if ($guild->shop)
            <div class="sidebar-item"><a href="{{ $guild->viewUrl . '/shop' }}" class="{{ set_active('*shop') }}">Shop</a></div>
        @endif
        <?php
        $pets_exists = class_exists('App\Models\Pet\Pet');
        $gear_exists = class_exists('App\Models\Claymore\Gear');
        ?>
        @if ($pets_exists)
            <div class="sidebar-item"><a href="{{ $guild->viewUrl . '/' . strtolower(__('guilds.playpen')) }}" class="{{ set_active('*' . strtolower(__('guilds.playpen'))) }}">{{ __('guilds.playpen') }}</a></div>
        @endif
        @if ($gear_exists)
            <div class="sidebar-item"><a href="{{ $guild->viewUrl . '/armory' }}" class="{{ set_active('*pets') }}">{{ __('guilds.playpen') }}</a></div>
        @endif
    </li>
    <li class="sidebar-section">
        <div class="sidebar-section-header">Members</div>
        <div class="sidebar-item"><a href="{{ $guild->viewUrl . '/members' }}" class="{{ set_active('*/members') }}">Members</a></div>
        <div class="sidebar-item"><a href="{{ $guild->viewUrl . '/characters' }}" class="{{ set_active('*characters') }}">Characters</a></div>
    </li>
    @if ($guild->owner_id === Auth::user()->id)
        <li class="sidebar-section">
            <div class="sidebar-section-header">Admin</div>
            <div class="sidebar-item"><a href="{{ $guild->editUrl }}" class="{{ set_active('*' . $guild->id . '/edit') }}">Settings</a></div>
            <div class="sidebar-item"><a href="{{ $guild->editRankUrl }}" class="{{ set_active('*edit-ranks') }}">Edit Ranks</a></div>
            <div class="sidebar-item"><a href="{{ $guild->viewUrl }}/manage-members" class="{{ set_active('*manage-members') }}">Manage Members</a></div>
            @if ($guild->shop)
                <div class="sidebar-item"><a href="{{ url(__('guilds.guilds') . '/' . $guild->id . '/shop/edit') }}" class="{{ set_active('*shop/edit') }}">Edit Shop</a></div>
            @else
                <div class="sidebar-item"><a href="{{ url(__('guilds.guilds') . '/' . $guild->id . '/shop/create') }}" class="{{ set_active('*shop/create') }}">Create Shop</a></div>
            @endif
        </li>
    @endif
</ul>
