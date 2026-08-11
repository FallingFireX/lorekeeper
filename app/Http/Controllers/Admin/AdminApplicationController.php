<?php

namespace App\Http\Controllers\Admin;

use App\Facades\Settings;
use App\Http\Controllers\Controller;
use App\Models\Submission\AdminApplication;
use App\Models\Team;
use App\Services\AdminApplicationService;
use Illuminate\Http\Request;

class AdminApplicationController extends Controller {
    /**
     * Shows the application index page.
     *
     * @param string $status
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getApplicationIndex(Request $request, $status = null) {
        $applications = AdminApplication::with('team')->where('status', $status ? ucfirst($status) : 'Pending');

        if (isset($data['sort'])) {
            switch ($data['sort']) {
                case 'newest':
                    $applications->sortNewest();
                    break;
                case 'oldest':
                    $applications->sortOldest();
                    break;
            }
        } else {
            $applications->sortOldest();
        }

        return view('admin.team.application_index', [
            'applications' => $applications->paginate(30)->appends($request->query()),
            'teams'        => Team::orderBy('id')->first(),
        ]);
    }

    /**
     * Shows the application detail page.
     *
     * @param int $id
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getApplication($id) {
        $applications = AdminApplication::where('id', $id)->first();

        if (!$applications) {
            abort(404);
        }

        return view('admin.team.application', [
            'applications'       => $applications,
            'teams'              => Team::orderBy('id')->first(),
            'settings'           => Settings::get('notify_staff_applicants'),
        ]);
    }

    /**
     * Accept or deny a application.
     *
     * @param mixed|null $id
     */
    public function postApplication(Request $request, $id, AdminApplicationService $service) {
        $application = AdminApplication::findOrFail($id);

        if (!in_array($request->input('status'), ['accepted', 'denied'])) {
            return redirect()->back()->with('error', 'Invalid status.');
        }

        $data = [
            'id'            => $application->id,
        ];

        $userId = auth()->id();

        if ($request->input('status') === 'accepted') {
            $service->acceptApplicant($data, $userId);
        } elseif ($request->input('status') === 'denied') {
            $service->rejectApplicant($data, $userId);
        }

        return redirect()->back()->with('success', 'Application '.strtolower($request->input('status')).' successfully.');
    }
}
