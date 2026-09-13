<?php

namespace App\Models\Guild;

use App\Models\Currency\Currency;
use App\Models\Currency\CurrencyLog;
use App\Models\Item\Item;
use App\Models\Model;
use App\Models\User\User;
use Carbon\Carbon;
use Auth;

class Guild extends Model {
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'owner_id', 'status', 'description',
        'parsed_description', 'location', 'reputation',
        'max_users', 'max_characters',
        'open_new_users', 'automatic_app_approval', 'open_inventory', 'open_bank', 'open_inventory', 'open_pets', 'open_armory',
        'has_logo', 'has_banner', 'is_disbanded',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'guilds';

    protected $casts = [
        'joined_at' => 'datetime',
    ];
    /**
     * Whether the model contains timestamps to be saved and updated.
     *
     * @var string
     */
    public $timestamps = true;

    /**
     * Validation rules for guild creation.
     *
     * @var array
     */
    public static $createRules = [
        'description' => 'nullable',
        'logo'        => 'nullable|image|mimes:png,gif|max:200',
        'banner'      => 'nullable|image|mimes:png,gif|max:800',
    ];

    /**
     * Validation rules for guild updating.
     *
     * @var array
     */
    public static $updateRules = [
        'description' => 'nullable',
        'logo'        => 'nullable|image|mimes:png,gif|max:200',
        'banner'      => 'nullable|image|mimes:png,gif|max:800',
    ];

    /**********************************************************************************************

        RELATIONS

    **********************************************************************************************/

    /**
     * Get the owner of the guild.
     */
    public function owner() {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * Get the guild inventory.
     */
    public function items() {
        return $this->belongsToMany(Item::class, 'guild_items')->withPivot('count', 'data', 'updated_at', 'id')->whereNull('guild_items.deleted_at');
    }

    /**
     * Gets the guild shop.
     */
    public function shop() {
        return $this->hasOne(GuildShop::class, 'guild_id');
    }

    /**
     * Get the characters attached to the guild.
     */
    public function characters() {
        return $this->hasMany(GuildCharacter::class, 'guild_id');
    }

    /**
     * Get the members in the guild.
     */
    public function members() {
        return $this->hasMany(GuildMember::class, 'guild_id');
    }

    /**
     * Get the mods in the guild.
     */
    public function mods() {
        return $this->hasMany(GuildMember::class, 'guild_id')->where('permissions', 1);
    }

    /**
     * Get the ranks in the guild.
     */
    public function ranks() {
        return $this->hasMany(GuildRank::class, 'guild_id');
    }

    /**********************************************************************************************

        SCOPES

    **********************************************************************************************/

    /**
     * Scope a query to only include pending submissions.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query) {
        return $query->where('status', 'Pending');
    }

    /**
     * Scope a query to only include drafted submissions.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeDrafts($query) {
        return $query->where('status', 'Drafts');
    }

    /**
     * Scope a query to only include viewable submissions.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param mixed|null                            $user
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeViewable($query, $user = null) {
        $forbiddenSubmissions = $this
            ->whereHas('prompt', function ($q) {
                $q->where('hide_submissions', 1)->whereNotNull('end_at')->where('end_at', '>', Carbon::now());
            })
            ->orWhereHas('prompt', function ($q) {
                $q->where('hide_submissions', 2);
            })
            ->orWhere('status', '!=', 'Approved')->pluck('id')->toArray();

        if ($user && $user->hasPower('manage_submissions')) {
            return $query;
        } else {
            return $query->where(function ($query) use ($user, $forbiddenSubmissions) {
                if ($user) {
                    $query->whereNotIn('id', $forbiddenSubmissions)->orWhere('user_id', $user->id);
                } else {
                    $query->whereNotIn('id', $forbiddenSubmissions);
                }
            });
        }
    }

    /**
     * Scope a query to sort submissions oldest first.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSortOldest($query) {
        return $query->orderBy('id');
    }

    /**
     * Scope a query to sort submissions by newest first.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSortNewest($query) {
        return $query->orderBy('id', 'DESC');
    }

    /**********************************************************************************************

        ACCESSORS

    **********************************************************************************************/

    /**
     * Gets the inventory of the user for selection.
     *
     * @param mixed $guild
     *
     * @return array
     */
    public function getInventory($guild) {
        return $this->data && isset($this->data['guild']['user_items']) ? $this->data['guild']['guild_items'] : [];
    }

    /**
     * Gets the currencies of the given user for selection.
     *
     * @param mixed $showAll
     *
     * @return array
     */
    public function getCurrencies($showAll = false) {
        $owned = GuildCurrency::where('guild_id', $this->id)->pluck('quantity', 'currency_id')->toArray();

        $currencies = Currency::where('is_guild_owned', 1);
        if ($showAll) {
            $currencies->where(function ($query) use ($owned) {
                $query->where('is_displayed', 1)->orWhereIn('id', array_keys($owned));
            });
        } else {
            $currencies = $currencies->where('is_displayed', 1);
        }

        $currencies = $currencies->orderBy('id', 'DESC')->get();

        foreach ($currencies as $currency) {
            $currency->quantity = $owned[$currency->id] ?? 0;
        }

        return $currencies;
    }

    /**
     * Get the guild's currency logs.
     *
     * @param int $limit
     *
     * @return \Illuminate\Pagination\LengthAwarePaginator|\Illuminate\Support\Collection
     */
    public function getCurrencyLogs($limit = 10) {
        $guild = $this;
        $query = CurrencyLog::with('currency')->where(function ($query) use ($guild) {
            $query->with('sender')->where('sender_type', 'Guild')->where('sender_id', $guild->id)->whereNotIn('log_type', ['Staff Grant', 'Prompt Rewards', 'Claim Rewards', 'Gallery Submission Reward']);
        })->orWhere(function ($query) use ($guild) {
            $query->with('recipient')->where('recipient_type', 'Guild')->where('recipient_id', $guild->id)->where('log_type', '!=', 'Staff Removal');
        })->orderBy('id', 'DESC');
        if ($limit) {
            return $query->take($limit)->get();
        } else {
            return $query->paginate(30);
        }
    }

    /**
     * Get the linked display name of the guild.
     *
     * @return string
     */
    public function getDisplayNameAttribute() {
        return '<a class="font-weight-bold text-primary" href="'.url(__('guilds.guilds').'/'.$this->id).'">'.$this->name.'</a>';
    }

    /**
     * Get the viewing URL of the guild.
     *
     * @return string
     */
    public function getViewUrlAttribute() {
        return url(__('guilds.guilds').'/'.$this->id);
    }

    /**
     * Get the editing URL of the guild.
     *
     * @return string
     */
    public function getEditUrlAttribute() {
        return url(__('guilds.guilds').'/'.$this->id.'/edit');
    }

    /**
     * Get the rank editing URL of the guild.
     *
     * @return string
     */
    public function getEditRankUrlAttribute() {
        return url(__('guilds.guilds').'/'.$this->id.'/edit-ranks/');
    }

    /**
     * Get the admin URL (for processing purposes) of the submission/claim.
     *
     * @return string
     */
    public function getAdminUrlAttribute() {
        return url('admin/guilds/edit/'.$this->id);
    }

    public function getGuildEditPermissions($user) {
        if ($user->id == $this->owner_id) {
            return true;
        }
        if ($this->mods()->where('user_id', $user->id)->count() > 0) {
            return true;
        }

        return false;
    }

    /**
     * Gets the file directory containing the model's image.
     *
     * @return string
     */
    public function getImageDirectoryAttribute() {
        return 'images/data/guilds/'.$this->id;
    }

    /**
     * Gets the file name of the model's image.
     *
     * @return string
     */
    public function getLogoFileNameAttribute() {
        return $this->id.'-logo.png';
    }

    /**
     * Gets the file name of the model's banner.
     *
     * @return string
     */
    public function getBannerFileNameAttribute() {
        return $this->id.'-banner.png';
    }

    /**
     * Gets the path to the file directory containing the model's image.
     *
     * @return string
     */
    public function getImagePathAttribute() {
        return public_path($this->imageDirectory);
    }

    /**
     * Gets the URL of the model's image.
     *
     * @return string
     */
    public function getLogoUrlAttribute() {
        if (!$this->has_logo) {
            return null;
        }

        return asset($this->imageDirectory.'/'.$this->LogoFileName);
    }

    /**
     * Gets the URL of the model's image.
     *
     * @return string
     */
    public function getBannerUrlAttribute() {
        if (!$this->has_banner) {
            return null;
        }

        return asset($this->imageDirectory.'/'.$this->BannerFileName);
    }

    /**
     * Gets the character's asset type for asset management.
     *
     * @return string
     */
    public function getAssetTypeAttribute() {
        return 'guild';
    }

    /**
     * Gets the character's log type for log creation.
     *
     * @return string
     */
    public function getLogTypeAttribute() {
        return 'Guild';
    }

    /**
     * Get the URL to accept guild Invitation.
     *
     * @return string
     */
    public function getInviteAcceptAttribute() {
        return url(__('guilds.guilds').'/'.$this->id.'/invite/accept');
    }

    /**
     * Get the URL to reject guild Invitation.
     *
     * @return string
     */
    public function getInviteRejectAttribute() {
        return url(__('guilds.guilds').'/'.$this->id.'/invite/reject');
    }

    /**********************************************************************************************

        OTHER FUNCTIONS

    **********************************************************************************************/

    public function getPermission() {
        if ( Auth::user()->isStaff || Auth::user()->id === $this->owner_id ) return true;

        if ( GuildMember::where([
            ['user_id', Auth::user()->id],
            ['guild_id', $this->id],
            ['permissions', '>', 0]
        ])->exists() ) return true;

        return false;
    }
}
