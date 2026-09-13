<?php

namespace App\Models\Currency;

use App\Models\Character\Character;
use App\Models\Guild\Guild;
use App\Models\Model;
use App\Models\User\User;

class CurrencyLog extends Model {
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'sender_id', 'sender_type',
        'recipient_id', 'recipient_type',
        'log', 'log_type', 'data',
        'currency_id', 'quantity',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'currencies_log';
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
        switch ($this->sender_type) {
            case 'User':
                return $this->belongsTo(User::class, 'sender_id');
            case 'Character':
                return $this->belongsTo(Character::class, 'sender_id');
            case 'Guild':
                return $this->belongsTo(Guild::class, 'sender_id');
            default:
                return $this->belongsTo(User::class, 'sender_id');
        }
    }

    /**
     * Get the user who received the logged action.
     */
    public function recipient() {
        switch ($this->recipient_type) {
            case 'User':
                return $this->belongsTo(User::class, 'recipient_id');
            case 'Character':
                return $this->belongsTo(Character::class, 'recipient_id');
            case 'Guild':
                return $this->belongsTo(Guild::class, 'recipient_id');
            default:
                return $this->belongsTo(User::class, 'recipient_id');
        }
    }

    /**
     * Get the currency that is the target of the action.
     */
    public function currency() {
        return $this->belongsTo(Currency::class);
    }
}
