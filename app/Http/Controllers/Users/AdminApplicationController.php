<?php

namespace App\Http\Controllers\Users;

use App\Facades\Settings;
use App\Http\Controllers\Controller;
use App\Models\Submission\AdminApplication;
use App\Models\Team;
use Illuminate\Http\Request;

class AdminApplicationController extends Controller {
    /**
     * Shows the application index page.
     *
     * @param string $status
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getIndex(Request $request, $status = null) {
        $applications = AdminApplication::where('user_id', auth()->id())
            ->orderBy('updated_at', 'desc')
            ->get();

        return view('home.admin_applications', [
            'applications' => $applications,
            'teams'        => Team::orderBy('id')->first(),
        ]);
    }

    /**
     * Shows the application page.
     *
     * @param int $id
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getApplication($id) {
        $applications = AdminApplication::where('id', $id)->where('status', '!=', 'Draft')->first();

        if (!$applications) {
            abort(404);
        }

        return view('home.admin_application', [
            'applications'   => $applications,
            'user'           => $applications->user,
            'teams'          => Team::orderBy('id')->first(),
            'is_read_only'   => Settings::get('is_applications_comment_read_only'),
        ]);
    }

    /**
     * Shows the application creation page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getNewApplication(Request $request) {
        // Get all teams that are open to applications
        $openTeams = Team::where('apps_open', true)
            ->orderBy('name', 'asc')
            ->get();

        return view('home.create_admin_application', [
            'teams' => $openTeams,
        ]);
    }

    public function postNewApplication(Request $request) {
        $request->validate([
            'team_id'     => 'required|exists:teams,id',
            'application' => 'required|string|max:5000',
        ]);

        $application = AdminApplication::create([
            'user_id'     => auth()->id(),
            'admin_id'    => null,
            'team_id'     => $request->input('team_id'),
            'application' => $request->input('application'),
            'status'      => 'pending',
        ]);

        return redirect()->to('applications/'.$application->id);
    }
}
