<?php

namespace App\Http\Controllers\Guilds;

use App\Facades\Settings;
use App\Http\Controllers\Controller;
use App\Models\Currency\Currency;
use App\Models\Guild\Guild;
use App\Models\Guild\GuildCurrency;
use App\Models\Guild\GuildItem;
use App\Models\Guild\GuildShop;
use App\Models\Guild\GuildShopLog;
use App\Models\Guild\GuildShopStock;
use App\Models\Item\Item;
use App\Models\Item\ItemCategory;
use App\Models\User\UserCurrency;
use App\Services\GuildManager;
use App\Services\GuildShopManager;
use Auth;
use Illuminate\Http\Request;

class GuildController extends Controller {
    /*
    |--------------------------------------------------------------------------
    | Queues Controller
    |--------------------------------------------------------------------------
    |
    | Displays information about queues as entered in the admin panel.
    | Pages displayed by this controller form the Queues section of the site.
    |
    */

    /**
     * Shows the index page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getGuildIndex(Request $request) {
        $query = Guild::query();
        $sort = $request->only(['sort']);

        if ($request->get('name')) {
            $query->where(function ($query) use ($request) {
                $query->where('guilds.name', 'LIKE', '%'.$request->get('name').'%');
            });
        }

        switch ($sort['sort'] ?? null) {
            default:
                $query->orderBy('created_at', 'DESC');
                break;
            case 'alpha':
                $query->orderBy('name');
                break;
            case 'alpha-reverse':
                $query->orderBy('name', 'DESC');
                break;
            case 'reputation':
                $query->orderBy('ranks.sort', 'DESC')->orderBy('name');
                break;
            case 'newest':
                $query->orderBy('created_at', 'DESC');
                break;
            case 'oldest':
                $query->orderBy('created_at', 'ASC');
                break;
        }

        return view('guilds.index', [
            'guilds'    => $query->paginate(30)->appends($request->query()),
        ]);
    }

    /**
     * Shows an individual guild.
     *
     * @param mixed $id
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getGuild(Request $request, $id) {
        $guild = Guild::where('id', $id)->first();

        if (!$guild) {
            abort(404);
        }

        return view('guilds.guild', [
            'guild' => $guild,
        ]);
    }

    /**
     * Shows the edit page for an individual guild.
     *
     * @param mixed $id
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getGuildEdit($id) {
        $guild = Guild::where('id', $id)->first();

        if (!$guild) {
            abort(404);
        }

        if (($guild->owner_id !== Auth::user()->id) || !Auth::user()->isStaff) {
            return redirect('/guilds/'.$guild->id)->with('error', 'You do not have permission to edit this guild.');
        }

        return view('guilds.guild_settings', [
            'guild'                 => $guild,
            'global_max_players'    => Settings::get('guilds_max_players'),
            'global_max_characters' => Settings::get('guilds_max_characters'),
            'members'               => $guild->members()
                ->join('users', 'guild_users.user_id', '=', 'users.id')
                ->pluck('users.name', 'guild_users.user_id')
                ->toArray(),
        ]);
    }

    /**
     * Shows the edit page for an individual guild.
     *
     * @param App\Services\GuildManager $service
     * @param int|null                  $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postGuildEdit(Request $request, GuildManager $service, $id = null) {
        $id ? $request->validate(Guild::$updateRules) : $request->validate(Guild::$createRules);
        $data = $request->only([
            'name', 'description', 'location', 'image', 'remove_image',
            'location', 'max_players', 'max_characters',
            'open_new_users', 'automatical_app_approval', 'open_inventory',
            'open_bank', 'open_pets', 'open_armory',
            'logo', 'remove_logo', 'banner', 'remove_banner',
        ]);

        $automatic_update = Settings::get('guilds_enable_automatic_updates');
        $enable_inventory = Settings::get('guilds_enable_inventory');
        $enable_shop = Settings::get('guilds_enable_shop');

        //Need to add validation to check if the site has guilds_enable_automatic_updates true before allowing this to directly post.
        //Do another auth check for owners/mods/staff here and return with error if false

        if ($id && $service->updateGuild(Guild::find($id), $data, Auth::user())) {
            flash('Guild updated successfully.')->success();
        } elseif (!$id && $category = $service->createGuild($data, Auth::user())) {
            flash('Guild created successfully.')->success();

            return redirect()->to('guilds/edit/'.$guild->id);
        } else {
            foreach ($service->errors()->getMessages()['error'] as $error) {
                flash($error)->error();
            }
        }

        return redirect()->back();
    }

    /**
     * Edits the guild staff.
     *
     * @param App\Services\GuildManager $service
     * @param int|null                  $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postGuildStaffEdit(Request $request, GuildManager $service, $id = null) {
        $id ? $request->validate(Guild::$updateRules) : $request->validate(Guild::$createRules);
        $data = $request->only([
            'owner_id', 'mods',
        ]);

        if ($id && $service->updateGuildStaff(Guild::find($id), $data, Auth::user())) {
            flash('Guild staff updated successfully.')->success();
        } else {
            foreach ($service->errors()->getMessages()['error'] as $error) {
                flash($error)->error();
            }
        }

        return redirect()->back();
    }

    /**
     * Shows the edit page for an individual guild.
     *
     * @param mixed $id
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getGuildEditRanks($id) {
        $guild = Guild::where('id', $id)->first();

        if (!$guild) {
            abort(404);
        }

        if (($guild->owner_id !== Auth::user()->id) || !Auth::user()->isStaff) {
            return redirect('/guilds/view'.$guild->id)->with('error', 'You do not have permission to edit this guild.');
        }

        return view('guilds.edit_ranks', [
            'guild' => $guild,
            'ranks' => [],
        ]);
    }

    /**
     * Shows the edit page for an individual guild.
     *
     * @param App\Services\GuildManager $service
     * @param int|null                  $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postGuildEditRanks(Request $request, GuildManager $service, $id = null) {
        $id ? $request->validate(Guild::$updateRules) : $request->validate(Guild::$createRules);
        $data = $request->only([
            'user_ranks', 'character_ranks',
        ]);

        \Log::info($data);

        if ($id && $service->updateGuildRanks(Guild::find($id), $data, Auth::user())) {
            flash('Guild ranks updated successfully.')->success();
        } elseif (!$id && $category = $service->updateGuildRanks($data, Auth::user())) {
            flash('Guild ranks created successfully.')->success();

            return redirect()->to('guilds/edit-ranks/'.$guild->id);
        } else {
            foreach ($service->errors()->getMessages()['error'] as $error) {
                flash($error)->error();
            }
        }

        return redirect()->back();
    }

    /**
     * Shows the guild shop page should the shop be active.
     *
     * @param mixed $id
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getGuildShop($id) {
        $guild = Guild::where('id', $id)->first();
        $shop = $guild->shop->first();

        if (!$guild || !$shop || !$shop->is_active) {
            abort(404);
        }

        $categories = ItemCategory::visible(Auth::check() ? Auth::user() : null)->orderBy('sort', 'DESC')->get();
        $query = $shop->displayStock()->where(function ($query) use ($categories) {
            $query->whereIn('item_category_id', $categories->pluck('id')->toArray())
                ->orWhereNull('item_category_id');
        });

        $items = count($categories) ? $query->orderByRaw('FIELD(item_category_id,'.implode(',', $categories->pluck('id')->toArray()).')')
            ->orderBy('name')
            ->get()
            ->groupBy('item_category_id') : $shop->displayStock()->orderBy('name')->get()->groupBy('item_category_id');

        return view('guilds.shop', [
            'guild'         => $guild,
            'shop'          => $shop,
            'categories'    => $categories->keyBy('id'),
            'items'         => $items,
            'currencies'    => Currency::whereIn('id', GuildShopStock::where('guild_shop_id', $shop->id)->pluck('currency_id')->toArray())->get()->keyBy('id'),
            //TODO: add shop variables and data
            //TODO: make shop not viewable if the shop is not active
        ]);
    }

    /**
     * Shows the guild character index.
     *
     * @param mixed $id
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getGuildCharacters(Request $request, $id) {
        $guild = Guild::where('id', $id)->first();

        $query = $guild->characters();
        $sort = $request->only(['sort']);
        $rank = $request->only(['rank']);

        // if ($request->get('name')) {
        //     $query->join('characters', 'guild_characters.character_id', '=', 'characters.id')
        //         ->where('characters.name', 'LIKE', '%'.$request->get('name').'%');
        // }

        // if ($rank && $rank !== '') {
        //     $query->where('rank', $rank);
        // }

        switch ($sort['sort'] ?? null) {
            default:
                $query->orderBy('joined_at', 'ASC');
                break;
            case 'alpha':
                $query->orderBy('name');
                break;
            case 'alpha-reverse':
                $query->orderBy('name', 'DESC');
                break;
            case 'reputation':
                //Do this one later it should grab from the character's currency
                break;
            case 'newest':
                $query->orderBy('joined_at', 'DESC');
                break;
            case 'oldest':
                $query->orderBy('joined_at', 'ASC');
                break;
        }

        return view('guilds.characters', [
            'guild'      => $guild,
            'characters' => $query->paginate(30)->appends($request->query()),
            'ranks'      => ['' => 'All Ranks'] + [], //Once guild ranks are built this should pull from that!
            //TODO Add guild character variables and data
        ]);
    }

    /**
     * Shows the guild member index, including member ranks.
     *
     * @param mixed $id
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getGuildMembers(Request $request, $id) {
        $guild = Guild::where('id', $id)->first();

        $query = $guild->members();
        $sort = $request->only(['sort']);
        $rank = $request->only(['rank']);

        // if ($request->get('name')) {
        //     $query->join('users', 'guild_users.user_id', '=', 'users.id')
        //         ->where('users.name', 'LIKE', '%'.$request->get('name').'%');
        // }

        // if ($rank !== '') {
        //     $query->where('rank', $rank);
        // }

        switch ($sort['sort'] ?? null) {
            default:
                $query->orderBy('joined_at', 'ASC');
                break;
            case 'alpha':
                $query->orderBy('name');
                break;
            case 'alpha-reverse':
                $query->orderBy('name', 'DESC');
                break;
            case 'reputation':
                //Do this one later to grab from the user's reputation (more advanced in case reputation is stored for multiple guilds)
                break;
            case 'newest':
                $query->orderBy('joined_at', 'DESC');
                break;
            case 'oldest':
                $query->orderBy('joined_at', 'ASC');
                break;
        }

        return view('guilds.members', [
            'guild'   => $guild,
            'members' => $query->paginate(30)->appends($request->query()),
            // TODO: Make the _member_table blade configurable with extra columns: Rank, # of characters, others?
        ]);
    }

    /**
     * Shows the guild inventory.
     *
     * @param mixed $id
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getGuildInventory($id) {
        $guild = Guild::where('id', $id)->first();
        $categories = ItemCategory::visible(Auth::check() ? Auth::user() : null)->orderBy('sort', 'DESC')->get();
        $itemOptions = Item::whereIn('item_category_id', $categories->pluck('id'));

        $items = count($categories) ?
            $guild->items()
                ->where('count', '>', 0)
                ->orderByRaw('FIELD(item_category_id,'.implode(',', $categories->pluck('id')->toArray()).')')
                ->orderBy('name')
                ->orderBy('updated_at')
                ->get()
                ->groupBy(['item_category_id', 'id']) :
            $guild->items()
                ->where('count', '>', 0)
                ->orderBy('name')
                ->orderBy('updated_at')
                ->get()
                ->groupBy(['item_category_id', 'id']);

        return view('guilds.inventory', [
            'guild'         => $guild,
            'categories'    => $categories->keyBy('id'),
            'items'         => $items,
            'logs'          => [] /* $guild->getItemLogs() */,
        ] + (Auth::check() && (Auth::user()->hasPower('edit_inventories') || Auth::user()->id == $this->character->user_id) ? [
            'itemOptions'       => $itemOptions->pluck('name', 'id'),
            'guildInventory'    => GuildItem::with('item')->whereIn('item_id', $itemOptions->pluck('id'))->whereNull('deleted_at')->where('count', '>', '0')->where('guild_id', $guild->id)->get()->filter(function ($guildItem) {
                return $guildItem->isTransferrable == true;
            })->sortBy('item.name'),
            'page'          => 'guild',
        ] : []));
    }

    /**
     * Shows the guild bank.
     *
     * @param mixed $id
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getGuildBank($id) {
        $guild = Guild::where('id', $id)->first();

        return view('guilds.bank', [
            'guild'                 => $guild,
            'currencies'            => $guild->getCurrencies($guild),
            'logs'                  => $guild->getCurrencyLogs(),
        ] + (Auth::check() && Auth::user()->id == $guild->owner_id ? [
            'takeCurrencyOptions' => Currency::where('allow_user_to_guild', 1)->where('is_guild_owned', 1)->where('is_user_owned', 1)->whereIn('id', GuildCurrency::where('guild_id', $guild->id)->pluck('currency_id')->toArray())->orderBy('id', 'DESC')->pluck('name', 'id')->toArray() ?? [],
            'giveCurrencyOptions' => Currency::where('allow_guild_to_user', 1)->where('is_guild_owned', 1)->where('is_user_owned', 1)->whereIn('id', UserCurrency::where('user_id', Auth::user()->id)->pluck('currency_id')->toArray())->orderBy('id', 'DESC')->pluck('name', 'id')->toArray() ?? [],

        ] : []) + (Auth::check() && (Auth::user()->hasPower('edit_inventories') || Auth::user()->id == $guild->owner_id) ? [
            'currencyOptions' => Currency::where('is_guild_owned', 1)->orderBy('id', 'DESC')->pluck('name', 'id')->toArray(),
        ] : []));
    }

    /**
     * Transfers currency between guild and user.
     *
     * @param App\Services\GuildManager $service
     * @param int|null                  $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postBuildBankTransfer(Request $request, GuildManager $service, $id = null) {
        $data = $request->only([
            'quantity', 'take_currency_id', 'give_currency_id',
        ]);

        if ($id && $service->updateGuildRanks(Guild::find($id), $data, Auth::user())) {
            flash('Guild ranks updated successfully.')->success();
        } elseif (!$id && $category = $service->updateGuildRanks($data, Auth::user())) {
            flash('Guild ranks created successfully.')->success();

            return redirect()->to('guilds/view/'.$guild->id.'/bank');
        } else {
            foreach ($service->errors()->getMessages()['error'] as $error) {
                flash($error)->error();
            }
        }

        return redirect()->back();
    }

    //Future TODO:
    /*
     * Guild armory
     * Guild events
     *
     */

    /** --------------------------------------------------------------
     * GUILD SHOPS.
     *
     * @param mixed $id
     * -------------------------------------------------------------- */

    /**
     * Shows the guild shop edit page.
     */
    public function getGuildShopCreateEdit($id) {
        $guild = Guild::where('id', $id)->first();
        $shop = $guild->shop ?? null;

        if (!$guild) {
            abort(404);
        }

        if (($guild->owner_id !== Auth::user()->id) || !Auth::user()->isStaff) {
            return redirect('/guilds/view'.$guild->id.'/shop')->with('error', 'You do not have permission to edit this guild shop.');
        }

        $guild_items = $guild->items()->get()->pluck('id')->toArray();

        return view('guilds.shop_edit', [
            'guild'      => $guild,
            'shop'       => $shop ?? null,
            'items'      => Item::whereIn('id', $guild_items)->orderBy('name')->pluck('name', 'id'),
            'currencies' => Currency::orderBy('name')->where('is_guild_owned', 1)->pluck('name', 'id'),
        ]);
    }

    /**
     * Creates or edits a guild shop.
     *
     * @param App\Services\GuildManager $service
     * @param int                       $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postCreateEditShop(Request $request, GuildManager $service, $id) {
        $data = $request->only([
            'name', 'description', 'image', 'remove_image', 'is_active',
        ]);

        $guild = Guild::find($id);

        if ($guild->shop && $service->updateShop($guild, $data, Auth::user())) {
            flash('Shop updated successfully.')->success();
        } elseif (!$guild->shop && $shop = $service->createShop($guild, $data, Auth::user())) {
            flash('Shop created successfully.')->success();

            return redirect()->to($guild->viewUrl.'/shop');
        } else {
            foreach ($service->errors()->getMessages()['error'] as $error) {
                flash($error)->error();
            }
        }

        return redirect()->back();
    }

    /**
     * Gets the shop stock modal.
     *
     * @param App\Services\GuildShopManager $service
     * @param int                           $id
     * @param int                           $stockId
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getShopStock(GuildShopManager $service, $id, $stockId) {
        $shop = GuildShop::where('id', $id)->where('is_active', 1)->first();
        if (!$shop) {
            abort(404);
        }

        $guild = $shop->guild()->first();
        $stock = GuildShopStock::with('item')->where('id', $stockId)->where('guild_shop_id', $id)->first();

        $user = Auth::user();
        $quantityLimit = 0;
        $userPurchaseCount = 0;
        $purchaseLimitReached = false;
        $guildOwned = null;
        $characters = collect();

        if ($user) {
            $quantityLimit = $service->getStockPurchaseLimit($stock, Auth::user());
            $userPurchaseCount = $service->checkUserPurchases($stock, Auth::user());
            $purchaseLimitReached = $service->checkPurchaseLimitReached($stock, Auth::user());
            if ($guild) {
                $guildOwned = GuildItem::where('guild_id', $guild->id)
                    ->where('item_id', $stock->item->id)
                    ->where('count', '>', 0)
                    ->get();

                $characters = $guild->characters()
                    ->whereHas('character', function ($query) use ($user) {
                        $query->where('user_id', $user->id);
                    })
                    ->with('character')
                    ->get()
                    ->map(function ($guildCharacter) {
                        return $guildCharacter->character;
                    })
                    ->filter();
            }
        }

        return view('guilds._stock_modal', [
            'guild'                => $guild,
            'shop'                 => $shop,
            'stock'                => $stock,
            'quantityLimit'        => $quantityLimit,
            'userPurchaseCount'    => $userPurchaseCount,
            'purchaseLimitReached' => $purchaseLimitReached,
            'guildOwned'           => $guild ? $guildOwned : null,
            'characters'           => $characters->pluck('fullName', 'id')->toArray(),
        ]);
    }

    /**
     * Edits a shop's stock.
     *
     * @param App\Services\GuildManager $service
     * @param int                       $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postEditShopStock(Request $request, GuildManager $service, $id) {
        $data = $request->only([
            'item_id', 'currency_id', 'cost', 'guild_cost', 'is_limited_stock', 'guild_only', 'quantity', 'purchase_limit',
        ]);

        if ($service->updateShopStock(Guild::find($id), $data, Auth::user())) {
            flash('Shop stock updated successfully.')->success();

            return redirect()->back();
        } else {
            foreach ($service->errors()->getMessages()['error'] as $error) {
                flash($error)->error();
            }
        }

        return redirect()->back();
    }

    /**
     * Buys an item from a shop.
     *
     * @param App\Services\GuildShopManager $service
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postBuy(Request $request, GuildShopManager $service) {
        $request->validate(GuildShopLog::$createRules);

        $data = $request->only(['stock_id', 'shop_id', 'guild_shop_id', 'slug', 'character_id', 'bank', 'quantity']);
        if (empty($data['guild_shop_id']) && !empty($data['shop_id'])) {
            $data['guild_shop_id'] = $data['shop_id'];
        }

        if ($service->buyStock($data, Auth::user())) {
            flash('Successfully purchased item.')->success();
        } else {
            foreach ($service->errors()->getMessages()['error'] as $error) {
                flash($error)->error();
            }
        }

        return redirect()->back();
    }

    /**
     * Shows the user's purchase history.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getPurchaseHistory() {
        return view('shops.purchase_history', [
            'logs'  => Auth::user()->getShopLogs(0),
            'shops' => Shop::where('is_active', 1)->orderBy('sort', 'DESC')->get(),
        ]);
    }

    /**
     * Buys an item from a shop.
     *
     * @param int                       $id
     * @param App\Services\GuildManager $service
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postDisbandGuild($id, Request $request, GuildManager $service) {
        $guild = Guild::find($id);

        if ($service->disbandGuild($guild, Auth::user())) {
            flash('Guild was successfully disbanded and members, characters and all removed.')->success();
        } else {
            foreach ($service->errors()->getMessages()['error'] as $error) {
                flash($error)->error();
            }
        }

        return redirect()->back();
    }
}
