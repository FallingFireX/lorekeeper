<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Gallery\GallerySubmission;
use App\Models\User\User;
use App\Models\Character\Character;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class ArtTracker extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'character_id', 'total', 'url', 'external_url',
        'notes', 'staff_comments', 'status', 'staff_id'
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'fp_submissions';

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'data' => 'array',
    ];

    /**
     * Whether the model contains timestamps to be saved and updated.
     *
     * @var string
     */
    public $timestamps = true;

    /**
     * Validation rules for submission creation.
     *
     * @var array
     */
    public static $createRules = [
        'url' => 'nullable|url',
        'external_url' => 'nullable|url',
    ];

    /**
     * Validation rules for submission updating.
     *
     * @var array
     */
    public static $updateRules = [
        'url' => 'nullable|url',
        'external_url' => 'nullable|url',
    ];

    /**********************************************************************************************

        RELATIONS

    **********************************************************************************************/

    /**
     * Get the user who made the submission.
     */
    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the staff who processed the submission.
     */
    public function staff() {
        return $this->belongsTo(User::class, 'staff_id');
    }

    /**
     * Get the characters attached to the submission.
     */
    public function characters() {
        return $this->belongsTo(Character::class, 'character_id');
    }

    public function isEditableBy($user)
    {
        return $this->status === 'Pending' && $this->user_id === $user->id;
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
     * Scope a query to sort submissions by newest first.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param mixed                                 $reverse
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSortNewest($query, $reverse = false) {
        return $query->orderBy('id', $reverse ? 'ASC' : 'DESC');
    }

    /**********************************************************************************************

        ACCESSORS

    **********************************************************************************************/

    /**
     * Get the viewing URL of the submission/claim.
     *
     * @return string
     */
    public function getViewUrlAttribute() {
        return url('fp/art-submissions/' .$this->id .'/edit' );
    }

    /**
     * Get the admin URL (for processing purposes) of the submission/claim.
     *
     * @return string
     */
    public function getAdminUrlAttribute() {
        return url('admin/fp-submissions/edit/'.$this->id );
    }

    /**
     * Get the rewards for the submission/claim.
     *
     * @return array
     */
    public function getRewardsAttribute() {
        if (isset($this->data['rewards'])) {
            $assets = parseAssetData($this->data['rewards']);
        } else {
            $assets = parseAssetData($this->data);
        }
        $rewards = [];
        foreach ($assets as $type => $a) {
            $class = getAssetModelString($type, false);
            foreach ($a as $id => $asset) {
                $rewards[] = (object) [
                    'rewardable_recipient' => 'User',
                    'rewardable_type'      => $class,
                    'rewardable_id'        => $id,
                    'quantity'             => $asset['quantity'],
                ];
            }
        }

        return $rewards;
    }

    /**
     * Gets the gallery submission (if there is one).
     */
    public function getGallerySubmissionAttribute() {
        if (!config('lorekeeper.settings.allow_gallery_submissions_on_prompts') || !isset($this->data['gallery_submission_id'])) {
            return null;
        }

        return GallerySubmission::find($this->data['gallery_submission_id'] ?? null);
    }

    public function getDeviantArtImageAttribute()
    {
        if (!$this->external_url) {
            return null;
        }

        // 1. Match standard links, stash links, or fav.me short links
        $isDeviantArt = str_contains($this->external_url, 'deviantart.com') || 
                        str_contains($this->external_url, 'fav.me');

        if (!$isDeviantArt) {
            return null;
        }

        // 2. Clear old cached null values during development by appending '_v2' 
        // to the key, or use Cache::forget('tracker_img_' . $this->id) if needed.
        return Cache::remember('tracker_img_v2_' . $this->id, 604800, function () {
            try {
                // 3. DeviantArt blocks requests without a custom browser User-Agent
                $response = Http::withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Laravel/ArtTracker'
                ])->timeout(5)->get('https://backend.deviantart.com/oembed', [
                    'url'    => $this->external_url,
                    'format' => 'json'
                ]);

                if ($response->successful()) {
                    $data = $response->json();
                    
                    // DeviantArt returns standard art files under 'url'
                    return $data['url'] ?? null;
                }
            } catch (\Exception $e) {
                // Logs any hidden API timeouts or errors to storage/logs/laravel.log
                \Log::error('DeviantArt oEmbed Fail: ' . $e->getMessage());
                return null; 
            }
            return null;
        });
    }
}
