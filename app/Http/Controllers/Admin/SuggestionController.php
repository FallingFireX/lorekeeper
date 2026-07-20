<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Suggestion;
use App\Models\Suggestion\SuggestionLabel;
use App\Models\Suggestion\SuggestionCategory;
use App\Services\SuggestionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SuggestionController extends Controller {
    
    /**
     * Shows the suggestion label index
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getLabelIndex() {
        return view('admin.suggestions.labels', [
            'labels' => SuggestionLabel::orderBy('id', 'DESC')->get(),
        ]);
    }

    /**
     * Shows the create label page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getCreateLabel() {
        return view('admin.suggestions.create_edit_label', [
            'label' => new SuggestionLabel,
        ]);
    }

    /**
     * Shows the edit label page.
     *
     * @param int $id
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getEditLabel($id) {
        $label = SuggestionLabel::find($id);
        if (!$label) {
            abort(404);
        }

        return view('admin.suggestions.create_edit_label', [
            'label' => $label,
        ]);
    }

    /**
     * Creates or edits a label.
     *
     * @param int|null                   $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postCreateEditLabel(Request $request, SuggestionService $service, $id = null) {
        $id ? $request->validate(SuggestionLabel::$updateRules) : $request->validate(SuggestionLabel::$createRules);
        $data = $request->only([
            'name', 'color', 'description',
        ]);
        if ($id && $service->updateLabel(SuggestionLabel::find($id), $data, Auth::user())) {
            flash('Label updated successfully.')->success();
        } elseif (!$id && $label = $service->createLabel($data, Auth::user())) {
            flash('Label created successfully.')->success();

            return redirect()->to('admin/suggestions/labels/edit/'.$label->id);
        } else {
            foreach ($service->errors()->getMessages()['error'] as $error) {
                flash($error)->error();
            }
        }

        return redirect()->back();
    }

    /**
     * Gets the label deletion modal.
     *
     * @param int $id
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getDeleteLabel($id) {
        $label = SuggestionLabel::find($id);

        return view('admin.suggestions._delete_suggestion_label', [
            'label' => $label,
        ]);
    }

    /**
     * Deletes a label
     *
     * @param App\Services\SuggestionService $service
     * @param int                        $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postDeleteLabel(Request $request, SuggestionService $service, $id) {
        if ($id && $service->deleteLabel(SuggestionLabel::find($id))) {
            flash('Label deleted successfully.')->success();
        } else {
            foreach ($service->errors()->getMessages()['error'] as $error) {
                flash($error)->error();
            }
        }

        return redirect()->to('admin/suggestions/labels');
    }

    /**
     * *******************************************************************************************
     * CATEGORIES                     
     * *******************************************************************************************
     */

    /**
     * Shows the suggestion label index
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getIndex() {
        return view('admin.suggestions.index', [
            'categories' => SuggestionCategory::orderBy('id', 'DESC')->get(),
        ]);
    }

    /**
     * Shows the create label page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getCreateCategory() {
        return view('admin.suggestions.create_edit_category', [
            'category' => new SuggestionCategory,
        ]);
    }

    /**
     * Shows the edit category page.
     *
     * @param int $id
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getEditCategory($id) {
        $category = SuggestionCategory::find($id);
        if (!$category) {
            abort(404);
        }

        return view('admin.suggestions.create_edit_category', [
            'category' => $category,
        ]);
    }

    /**
     * Creates or edits a category.
     *
     * @param int|null                   $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postCreateEditCategory(Request $request, SuggestionService $service, $id = null) {
        $id ? $request->validate(SuggestionCategory::$updateRules) : $request->validate(SuggestionCategory::$createRules);
        $data = $request->only([
            'name', 'is_visible', 'description',
        ]);
        if ($id && $service->updateCategory(SuggestionCategory::find($id), $data, Auth::user())) {
            flash('Category updated successfully.')->success();
        } elseif (!$id && $category = $service->createCategory($data, Auth::user())) {
            flash('Category created successfully.')->success();

            return redirect()->to('admin/suggestions/categories/edit/'.$category->id);
        } else {
            foreach ($service->errors()->getMessages()['error'] as $error) {
                flash($error)->error();
            }
        }

        return redirect()->back();
    }

    /**
     * Gets the Category deletion modal.
     *
     * @param int $id
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getDeleteCategory($id) {
        $category = SuggestionCategory::find($id);

        return view('admin.suggestions._delete_suggestion_category', [
            'category' => $category,
        ]);
    }

    /**
     * Deletes a category
     *
     * @param App\Services\SuggestionService $service
     * @param int                        $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postDeleteCategory(Request $request, SuggestionService $service, $id) {
        if ($id && $service->deleteCategory(SuggestionCategory::find($id))) {
            flash('Category deleted successfully.')->success();
        } else {
            foreach ($service->errors()->getMessages()['error'] as $error) {
                flash($error)->error();
            }
        }

        return redirect()->to('admin/suggestions/categories');
    }
    
}
