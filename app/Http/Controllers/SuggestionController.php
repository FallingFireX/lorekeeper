<?php

namespace App\Http\Controllers;

use App\Models\Suggestion\Suggestion;
use App\Models\Suggestion\SuggestionCategory;
use App\Models\Suggestion\SuggestionTag;
use App\Models\Suggestion\SuggestionLabel;
use App\Services\SuggestionService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Http\Request;

class SuggestionController extends Controller {
    
    /**
     * Shows the suggestion index.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getIndex() {

        return view('suggestions.index', [
            'labels'        => SuggestionLabel::orderBy('id', 'DESC')->get(),
            'category'      => SuggestionCategory::orderBy('id', 'DECS')->get(),
            'suggestion'    => Suggestion::orderBy('id', 'DECS')->get(),
            ]);
    }

    /**
     * Shows a suggestion.
     *
     * @param int         $id
     * @param string|null $slug
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getSuggestion($id, $slug = null) {
        $suggestion = Suggestion::find($id);
        if (!$suggestion) {
            abort(404);
        }

        return view('suggestions.suggestion', ['suggestion' => $suggestion]);
    }

    public function getCreateSuggestion(){
        return view('home.create_edit_suggestion', [
            'suggestion' => new Suggestion,
            'categories' => SuggestionCategory::orderBy('id', 'DESC')->pluck('name', 'id')->toArray(),
        ]);
    }

    /**
     * Shows the edit label page.
     *
     * @param int $id
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getEditSuggestion($id) {
        $suggestion = Suggestion::find($id);
        if (!$suggestion) {
            abort(404);
        }

        return view('home.create_edit_suggestion', [
            'suggestion' => $suggestion,
        ]);
    }

    /**
     * Creates or edits a label.
     *
     * @param int|null                   $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postCreateEditSuggestion (Request $request, SuggestionService $service, $id = null) {
        $request->validate(Suggestion::$createRules);
        if ($submission = $service->createSuggestion($request->only(['title', 'user_id', 'category_id', 'text']), Auth::user())) {
            flash('Prompt submitted successfully.')->success();

            return redirect()->to('suggestions/view/'.$submission->id);
        } else {
            foreach ($service->errors()->getMessages()['error'] as $error) {
                flash($error)->error();
            }

            return redirect()->back()->withInput();
        }

        return redirect()->to('submissions');
    }
}
