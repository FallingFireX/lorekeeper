<?php

namespace App\Models\Guild;

use App\Models\Model;
use App\Models\User\User;

class GuildInvitation extends Model {
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'guild_id', 'user_id',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'guild_invitations';

    /**
     * Whether the model contains timestamps to be saved and updated.
     *
     * @var string
     */
    protected $casts = [
        'expires_at' => 'datetime',
    ];

    public $timestamps = true;

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
}
