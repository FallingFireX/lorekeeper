<?php

namespace App\Models\Suggestion;

use App\Models\Model;
use App\Models\User\User;
use Illuminate\Support\Facades\DB;

class Suggestion extends Model {
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'category_id', 'title', 'text',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'suggestions';

    /**
     * Whether the model contains timestamps to be saved and updated.
     *
     * @var string
     */
    public $timestamps = true;

    /**
     * Validation rules for creation.
     *
     * @var array
     */
    public static $createRules = [
        'title'               => 'required|between:10,255',   //Titles should be reasonably understandable. so they require a longer minimum length. Modify this if your users are making titles too short
        'text'                => 'required',
    ];

    /**
     * Validation rules for updating.
     *
     * @var array
     */
    public static $updateRules = [
        'name'                => 'required|between:10,255',
        'description'         => 'required',
    ];

    /**********************************************************************************************

        SCOPES

    **********************************************************************************************/

    /**
     * Scope a query to sort suggestions in alphabetical order.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSortAlphabetical($query, $reverse = false) {
        return $query->orderBy('name', $reverse ? 'DESC' : 'ASC');
    }

    /**
     * Scope a query to sort suggestions by newest first.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSortNewest($query) {
        return $query->orderBy('id', 'DESC');
    }

    /**
     * Scope a query to sort suggestions oldest first.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSortOldest($query) {
        return $query->orderBy('id');
    }

    /**********************************************************************************************

        RELATIONS

    **********************************************************************************************/

    /**
     * Get the user who made the suggestion.
     */
    public function user() {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the category this suggestion belongs to
     */
    public function category() {
        return $this->belongsTo(SuggestionCategory::class, 'category_id');
    }

    /**
     * Scope a query to sort suggestions in category order.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSortCategory($query) {
        if (SuggestionCategory::all()->count()) {
            return $query->orderBy( SuggestionCategory::select('sort')->whereColumn('suggestion.category_id', 'categories.id'), 'DESC');
        }

        return $query;
    }

    /**********************************************************************************************

        OTHER FUNCTIONS

    **********************************************************************************************/

    /**
     * Checks if the suggestion has a particular tag.
     *
     * @param mixed $tag
     *
     * @return bool
     */
    public function hasTag($tag) {
        return $this->tags()->where('tag', $tag)->exists();
    }

    /**
     * Gets a particular tag attached to the suggestion.
     *
     * @param mixed $tag
     *
     * @return \App\Models\Suggestion\SuggestionTag
     */
    public function tag($tag) {
        return $this->tags()->where('tag', $tag)->first();
    }

}
