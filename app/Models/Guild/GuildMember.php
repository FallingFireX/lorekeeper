<?php

namespace App\Models\Guild;

use App\Models\Model;
use App\Models\User\User;

class GuildMember extends Model {
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'guild_id', 'user_id', 'rank_id', 'reputation', 'joined_at',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'guild_users';

    /**
     * Whether the model contains timestamps to be saved and updated.
     *
     * @var string
     */
    protected $casts = [
        'joined_at' => 'datetime',
    ];

    public $timestamps = false;

    /**********************************************************************************************

        RELATIONS

    **********************************************************************************************/

    /**
     * Get the guild.
     */
    public function guild() {
        return $this->belongsTo(Guild::class, 'guild_id');
    }

    /**
     * Get the item associated with this item stack.
     */
    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the guild rank associated with the member.
     */
    public function rank() {
        return $this->belongsTo(GuildRank::class, 'rank_id')
            ->where('guild_id', $this->guild_id);
    }

    /**********************************************************************************************

        ACCESSORS

    **********************************************************************************************/

    public function getPermissionsNameAttribute() {
        switch ($this->permissions) {
            case 2:
                return 'Owner';
            case 1:
                return 'Moderator';
            case 0:
            default:
                return 'Member';
        }
    }

    /**********************************************************************************************

        OTHER FUNCTIONS

    **********************************************************************************************/

    public function isOwner() {
        if ($this->permissions > 1) {
            return true;
        }

        return false;
    }

    public function isMod() {
        if ($this->permissions < 2 && $this->permissions > 0) {
            return true;
        }

        return false;
    }
}
