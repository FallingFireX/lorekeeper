<?php

namespace App\Models\User;

use App\Models\Team;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserTeam extends Model {
    use HasFactory;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'type',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user_admin_role';

    /**
     * The relationships that should always be loaded.
     *
     * @var array
     */
    protected $with = ['team'];

    /**********************************************************************************************

        ACCESSORS

    **********************************************************************************************/

    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function team() {
        return $this->belongsTo(Team::class, 'team_id');
    }

    public function getRolePriorityAttribute() {
        return match ($this->type ?? 'Primary') {
            'Lead'      => 1,
            'Primary'   => 2,
            'Secondary' => 3,
            'Trainee'   => 4,
            default     => 2,
        };
    }
}
