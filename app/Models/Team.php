<?php

namespace App\Models;

use App\Models\User\UserTeam;
use Illuminate\Database\Eloquent\Model;

class Team extends Model {
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'type', 'relation', 'apps_open', 'description', 'responsibilities',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'teams';

    /**
     * Whether the model contains timestamps to be saved and updated.
     *
     * @var string
     */
    public $timestamps = true;

    /**
     * Validation rules for team creation.
     *
     * @var array
     */
    public static $createRules = [
        'name'             => 'unique:teams|required|between:2,225',
        'description'      => 'nullable',
        'responsibilities' => 'nullable',
    ];

    /**
     * Validation rules for team updating.
     *
     * @var array
     */
    public static $updateRules = [
        'name'           => 'required|between:2,225',
        'apps_open'      => 'nullable|boolean',
        'description'    => 'nullable',
        'relation'       => 'nullable',
    ];

    /**********************************************************************************************

        ACCESSORS

    **********************************************************************************************/

    /**
     * Gets the admin edit URL.
     *
     * @return string
     */
    public function getAdminUrlAttribute() {
        return url('admin/data/teams/edit/'.$this->id);
    }

    /**
     * Gets the power required to edit this model.
     *
     * @return string
     */
    public function getAdminPowerAttribute() {
        return 'edit_teams';
    }

    /**
     * Displays the model's name, linked to its encyclopedia page.
     *
     * @return string
     */
    public function getDisplayNameAttribute() {
        return '<a href="'.$this->url.'" class="display-team">'.$this->name.'</a>';
    }

    public function users() {
        return $this->belongsToMany(User::class, 'user_admin_role', 'team_id', 'user_id')
            ->withPivot('type')
            ->withTimestamps();
    }

    public function userTeams() {
        return $this->hasMany(UserTeam::class, 'team_id');
    }

    public function parent() {
        return $this->belongsTo(self::class, 'relation');
    }

    public function children() {
        return $this->hasMany(self::class, 'relation');
    }
}
