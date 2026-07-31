<?php

namespace App\Http\Controllers\Admin;

use App\Models\ArtTracker;
use Illuminate\Routing\Controller;
use App\Models\Character\Character;
use App\Facades\Notifications;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Arr;
use DB;

class ArtTrackerController extends Controller {

    /**
     * Shows a character's tracker.
     *
     * @param string $slug
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getTrackerQueue(Request $request, $status = null){
        $submissions = ArtTracker::where('status', $status ? ucfirst($status) : 'Pending');
        
        return view('admin.art_submissions.index', [
            'submissions'       => $submissions->paginate(30)->appends($request->query()),
            'user'              => Auth::user() ?? null,
            'character'         => Character::visible(Auth::user() ?? null)->myo(0)->orderBy('slug', 'DESC')->get()->pluck('fullName', 'slug')->toArray(),
        ]);
    }

     /**
     * Shows a individual entry for approval or denial.
     *
     * @param mixed  $id
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getTrackerSubmission($id){
        $submission = ArtTracker::where('id', $id)->where('status', '!=', 'Draft')->firstOrFail();

        return view('admin.art_submissions.tracker', [
            'submission'      => $submission,
            'user'            => Auth::user() ?? null,
            'character'       => Character::visible(Auth::user() ?? null)->myo(0)->where('id', $submission->character_id)->first(),
            'ajax'            => true,
        ]);
    }

    public function postTrackerSubmission(Request $request, $id, $action) {
        $submission = ArtTracker::findOrFail($id);

        // Prevent double-processing
        if ($submission->status == 'Approved' || $submission->status == 'Rejected') {
            return redirect()->back()->with('error', 'This submission has already been processed.');
        }

        return DB::transaction(function () use ($submission, $action, $request) {
            switch ($action) {
                case 'approve':
                    //Update character points
                    $submission->update(['status' => 'Approved','staff_comments' => $request->get('staff_comments'),'staff_id' => Auth::user()->id,]);
                    $points = $submission->total ?? 0;
                    $submission->characters()->increment('total_fp', $points);

                    //Check character rank and update it if needed
                    $submission->characters->refresh();
                    $submission->characters->updateRank();
                    $message = 'Submission approved!';

                    //Send notification
                    $staff = Auth::user(); 
                    if ($submission->user) {
                        Notifications::create('TRACKER_APPROVED', $submission->user, [
                            'staff_url'     => $staff->url,
                            'staff_name'    => $staff->name,
                            'tracker_id'    => $submission->id,
                            'slug'          => $submission->characters->slug,
                        ]);
                    }

                    break;

                case 'reject':
                    $submission->update(['status' => 'Rejected','staff_comments' => $request->get('staff_comments'),'staff_id' => Auth::user()->id,]);

                    $staff = Auth::user(); 
                    if ($submission->user) {
                        Notifications::create('TRACKER_REJECTED', $submission->user, [
                            'staff_url'     => $staff->url,
                            'staff_name'    => $staff->name,
                            'tracker_id'    => $submission->id,
                            'slug'          => $submission->characters->slug,
                        ]);
                    }

                    $message = 'Submission rejected.';
                    break;

                case 'cancel':
                    $submission->update(['status' => 'Cancelled']);
                    $message = 'Submission cancelled.';
                    break;

                default:
                    return redirect()->back()->with('error', 'Invalid action.');
            }

            return redirect()->back()->with('success', $message);
        });
    }


    //TODO: Update on approval or rejection
    //TODO: Update character table on approval
    //Index page
    //Individual view page


    /**
     * Allows a user to update a submission
     */
    public function update(Request $request, $id) 
    {
        $submission = ArtTracker::findOrFail($id);

        if (!auth()->user()->is_admin && !$submission->isEditableBy(auth()->user())) {
            abort(403);
        }

        $validated = $request->validate([
            'character_id' => 'required|exists:characters,id', 

                'art_type' => 'nullable|string|in:none,headshot,partial,fullbody',
                'tags' => 'nullable|array',
                'tags.*.name' => 'nullable|string',
                'tags.*.applied_value' => 'required|numeric',
                'bonuses' => 'nullable|array',
                'bonuses.*.name' => 'required|string',
                'bonuses.*.value' => 'required|numeric',
                'literature' => 'nullable',
                'literature.word_count' => 'nullable|integer',
                'literature.points' => 'nullable|integer',

                'total' => 'required|numeric',
                'multiplier' => 'required|integer',
        ]);

        $submission->update([
            'character_id' => $validated['character_id'],
            'data'         => Arr::except($validated, ['character_id']),
        ]);

        return response()->json(['message' => 'Submission updated successfully.']);
    }

    /**
     * Edits a tracker submission
     */
    public function edit($id){
        $user = Auth::user();
        $users_character = Auth::user()->characters()->with('image')->visible()->whereNotNull('name')->whereNull('trade_id')->pluck('name', 'id')->toArray();
        $user_galleries = Auth::user()->gallerySubmissions()->where('status', 'Approved')->pluck('title', 'id');

        $submission = ArtTracker::where('id', $id)
            ->where('user_id', auth()->id())
            ->where('status', 'Pending')
            ->firstOrFail();

        return view('calculator.calculator', [
            'editing' => true,
            'submission' => $submission,
            'users_character'   => $users_character,
            'user_galleries'    => $user_galleries,
        ]);
    }
}
