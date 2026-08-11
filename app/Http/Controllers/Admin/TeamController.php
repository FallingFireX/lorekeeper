<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Team;
use Illuminate\Http\Request;

class TeamController extends Controller {
    /*
    |--------------------------------------------------------------------------
    | Team/Department controller
    |--------------------------------------------------------------------------
    |
    | Handles creation/editing of teams.
    |
    */

    /**
     * Shows the team index.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getIndex() {
        return view('admin.team.index', [
            'teams' => Team::orderBy('id')->get(),
        ]);
    }

    /**
     * Shows the create team page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getCreateTeam() {
        return view('admin.team.create_edit_team', [
            'teams'       => new Team,
        ]);
    }

    /**
     * Shows the create team page.
     *
     * @param mixed $id
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getEditTeam($id) {
        $team = Team::find($id);
        if (!$team) {
            abort(404);
        }
        $allTeams = Team::where('id', '!=', $team->id)->pluck('name', 'id');

        return view('admin.team.create_edit_team', [
            'teams'    => $team,
            'allTeams' => $allTeams,

        ]);
    }

    /**
     * Creates or edits a team.
     *
     * @param mixed|null $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postCreateEditTeam(Request $request, $id = null) {
        $request->validate([
            'name'                   => 'required|between:2,225',
            'apps_open'              => 'nullable|boolean',
            'description'            => 'nullable',
            'relation'               => 'nullable',
            'responsibilities'       => 'nullable',
        ]);

        if ($id) {
            // Editing an existing item
            $team = Team::findOrFail($id);
            $team->update($request->all());
            flash('Team updated successfully.')->success();
        } else {
            // Creating a new item
            $team = Team::create($request->all());
            flash('Team created successfully.')->success();

            return redirect()->to('admin/teams/edit/'.$team->id);
        }

        return redirect()->back();
    }

    /**
     * Gets the team deletion modal.
     *
     * @param int $id
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getDeleteTeam($id) {
        $team = Team::find($id);

        return view('admin.team._delete_team', [
            'teams' => $team,
        ]);
    }

    /**
     * Deletes a team.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postDeleteTeam(Request $request, $id) {
        $team = Team::find($id);

        if (!$team) {
            flash('Invalid team selected.')->error();

            return redirect()->to('admin/teams');
        }

        try {
            $team->delete();
            flash('Team deleted successfully.')->success();
        } catch (\Exception $e) {
            flash('An error occurred while deleting the team: '.$e->getMessage())->error();
        }

        return redirect()->to('admin/teams');
    }
}
