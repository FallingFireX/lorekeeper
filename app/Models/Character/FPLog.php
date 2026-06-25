<?php

namespace App\Models\Character;

use App\Models\Model;
use App\Models\User\User;

class FPLog extends Model {
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'character_id', 'admin_id', 'reason', 'source', 'amount', 'recipient_url',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'fp_logs';


    /**
     * Whether the model contains timestamps to be saved and updated.
     *
     * @var string
     */
    public $timestamps = true;

    /**********************************************************************************************

        RELATIONS

    **********************************************************************************************/

    /**
     * Get the user who initiated the logged action.
     */
    public function sender() {
        return $this->belongsTo(User::class, 'admin_id');
    }

    /**
     * Get the character that is the target of the action.
     */
    public function character() {
        return $this->belongsTo(Character::class);
    }

    /**********************************************************************************************

        ACCESSORS

    **********************************************************************************************/

}
