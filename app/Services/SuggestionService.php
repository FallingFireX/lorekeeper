<?php

namespace App\Services;

use App\Models\Suggestion\Suggestion;
use App\Models\Suggestion\SuggestionLabel;
use App\Models\Suggestion\SuggestionCategory;
use App\Models\Suggestion\SuggestionTag;
use Illuminate\Support\Facades\DB;

class SuggestionService extends Service {
    /*
    |--------------------------------------------------------------------------
    | Suggestion Service
    |--------------------------------------------------------------------------
    |
    | Handles the creation and editing of suggestion labels and categories.
    |
    | This largely exists for potential updates later
    |
    */

    /**
     * Creates a new label.
     *
     * @param array                 $data
     * @param \App\Models\User\User $user
     *
     * @return \App\Models\Suggestion|bool
     */
    public function createLabel($data) {
        DB::beginTransaction();

        try {
            $data = $this->populateLabelData($data);

            $label = SuggestionLabel::create($data);

            return $this->commitReturn($label);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }

    /**
     * Creates a new categroy.
     *
     * @param array                 $data
     * @param \App\Models\User\User $user
     *
     * @return \App\Models\Suggestion|bool
     */
    public function createCategory($data) {
        DB::beginTransaction();

        try {
            $data = $this->populateCategoryData($data);

            $category = SuggestionCategory::create($data);

            return $this->commitReturn($category);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }

    /**
     * Updates a Label.
     *
     * @param \App\Models\Rarity    $rarity
     * @param array                 $data
     * @param \App\Models\User\User $user
     *
     * @return \App\Models\Rarity|bool
     */
    public function updateLabel($label, $data, $user) {
        DB::beginTransaction();

        try {
            // More specific validation
            if (SuggestionLabel::where('name', $data['name'])->where('id', '!=', $label->id)->exists()) {
                throw new \Exception('The name has already been taken.');
            }

            $data = $this->populateLabelData($data, $label);

            $label->update($data);
           
            return $this->commitReturn($label);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }

    /**
     * Updates a Label.
     *
     * @param \App\Models\Rarity    $rarity
     * @param array                 $data
     * @param \App\Models\User\User $user
     *
     * @return \App\Models\Rarity|bool
     */
    public function updateCategory($category, $data, $user) {
        DB::beginTransaction();

        try {
            // More specific validation
            if (SuggestionCategory::where('name', $data['name'])->where('id', '!=', $category->id)->exists()) {
                throw new \Exception('The name has already been taken.');
            }

            $data = $this->populateCategoryData($data, $category);

            $category->update($data);
           
            return $this->commitReturn($category);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }


    /**
     * Deletes a label.
     *
     * @param \App\Models\suggestion $label
     *
     * @return bool
     */
    public function deleteLabel($label) {
        DB::beginTransaction();

        try {
            // Checks to see if a label exists on a suggestion and prevents it if so
            if (SuggestionTag::where('label_id', $label->id)->exists()) {
                throw new \Exception('This label is being used on one or more suggestions');
            }

            $label->delete();

            return $this->commitReturn(true);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }

    /**
     * Deletes a label.
     *
     * @param \App\Models\suggestion $label
     *
     * @return bool
     */
    public function deleteCategory($category) {
        DB::beginTransaction();

        try {
            // Checks to see if a label exists on a suggestion and prevents it if so
            if (Suggestion::where('category_id', $category->id)->exists()) {
                throw new \Exception('This category has been assigned to at least one or more suggestions');
            }

            $category->delete();

            return $this->commitReturn(true);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }

    
    /**
     * Processes staff input for creating/updating a label.
     *
     * @param array              $data
     * @param \App\Models\Suggestion $label
     *
     * @return array
     */
    private function populateLabelData($data, $label = null) {

        if (isset($data['color'])) {
            $data['color'] = str_replace('#', '', $data['color']);
        }

        return $data;
    }

    /**
     * Processes staff input for creating/updating a label.
     *
     * @param array              $data
     * @param \App\Models\Suggestion $label
     *
     * @return array
     */
    private function populateCategoryData($data, $category = null) {

        return $data;
    }
}
