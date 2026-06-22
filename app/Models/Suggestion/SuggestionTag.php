<?php

namespace App\Models\Suggestion;

use App\Models\Model;
use Illuminate\Support\Facades\Config;

class SuggestionTag extends Model {
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'suggestion_id', 'label_id'
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'suggestions_tags';

    /**********************************************************************************************

        RELATIONS

    **********************************************************************************************/

    /**
     * Get the item that this tag is attached to.
     */
    public function item() {
        return $this->belongsTo(Suggestion::class);
    }

    /**********************************************************************************************

        SCOPES

    **********************************************************************************************/

    /**
     * Scope a query to retrieve only a certain tag.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string                                $tag
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeType($query, $tag) {
        return $query->where('tag', $tag);
    }

    /**********************************************************************************************

        ACCESSORS

    **********************************************************************************************/

    /**
     * Displays the tag based on the label color
     *
     * @return string
     */
    public function getDisplayTagAttribute() {
        return '<span class="badge" '.($this->color ? 'style="color: #'.$this->color.';"' : '').'>'.$this->name.'</a>';
    }


    /**********************************************************************************************

        OTHER FUNCTIONS

    **********************************************************************************************/

   
}
