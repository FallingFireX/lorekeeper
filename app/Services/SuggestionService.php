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
    | Handles the creation and editing of suggestions, labels and categories.
    |
    | Additionally, handles deletion and checking for valid deletion conditions
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
     * Deletes a category.
     *
     * @param \App\Models\suggestion $label
     *
     * @return bool
     */
    public function deleteCategory($category) {
        DB::beginTransaction();

        try {
            // Checks to see if a category exists on a suggestion and prevents it if so
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


    public function createSuggestion($data, $user) {
        DB::beginTransaction();

        $categoryId = request('category_id'); 

        // Find the category by that specific ID
        $category = SuggestionCategory::find($categoryId);


        try {
            if (!isset($data['category_id'])) {
                throw new \Exception('Please select a category.');
            }

            if (!$category) {
                throw new \Exception('Category not found.');
            }

            // Step 3: Check visibility
            if (!$category->is_visible) {
                throw new \Exception('This Category is closed for suggestions');
            }
            
            $suggestion = Suggestion::create([
                'user_id'   => $user->id,
                'category_id' => $category->id,
                'title'     => $data['title'],
                'text'      => $data['text'],
                'data'      => null,
            ]);

            return $this->commitReturn($suggestion);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }

}
