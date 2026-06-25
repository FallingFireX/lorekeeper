<?php

namespace App\Http\Controllers;

use App\Models\ArtTracker;
use Illuminate\Routing\Controller as BaseController;
use App\Models\Character\Character;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Arr;

class calculatorController extends BaseController {

    public function store(Request $request){
        logger()->info('STORE HIT', $request->all());

        $validated = $request->validate([
            'character_id' => 'required|exists:characters,id',
            'total' => 'required|numeric',
            'external_url' => 'nullable|url',
            'notes'         => 'nullable|string'
        ]);

        try {
            $user = auth()->user();

            if (!$user || !$user->settings) {
                abort(500, 'User or settings missing');
            }

            if ($user->settings->fp_submissions < 1) {
                return response()->json([
                    'success' => false,
                    'message' => "You can't submit more FP this month."
                ], 403);
            }

            ArtTracker::create([
                'user_id' => auth()->id(),
                'status' => 'Pending',
                'url'          => $request->input('url'),
                'external_url'          => $request->input('external_url'),
                'character_id' => $validated['character_id'],
                'notes'         =>$validated['notes'],
                'total'         => $validated, ['total'],
            ]);
            $user->settings->fp_submissions -= 1;
            $user->settings->save();

            return response()->json([
                'success' => true,
                'message' => 'Art submission successfully stored!'
            ]);

        } catch (\Throwable $e) {
            logger()->error('STORE FAILED', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
        
        return response()->json(['success' => true]);
    }

    public function update(Request $request, $id) {
    
        $submission = ArtTracker::findOrFail($id);

        if (!auth()->user()->is_admin && !$submission->isEditableBy(auth()->user())) {
            abort(403);
        }
        
        $validated = $request->validate([
            'character_id' => 'required|exists:characters,id', 
            'total' => 'required|numeric',
            'external_url' => 'nullable|url',
            'notes'         => 'nullable|string'
        ]);

        $submission->update([
            'url'          => $request->input('url'),
            'external_url' => $request->input('external_url'),
            'character_id' => $validated['character_id'],
            'notes'         =>$validated['notes'],
            'total'         => $validated, ['total'],
        ]);

        return response()->json(['message' => 'Submission updated successfully.']);
    }

    public function edit($id){
        $user = Auth::user();

        $query = ArtTracker::where('id', $id)->where('status', 'Pending');

        if (!$user->isStaff) {
            $query->where('user_id', $user->id);
        }

        $submission = $query->firstOrFail();

        $all_characters = Character::with('image') ->visible() ->whereNotNull('name') ->whereNull('trade_id') ->pluck('name', 'id') ->toArray(); 

        $users_character = $user->characters()
            ->with('image')
            ->visible()
            ->whereNotNull('name')
            ->whereNull('trade_id')
            ->pluck('name', 'id')
            ->toArray();

        $user_galleries = $user->gallerySubmissions()
            ->where('status', 'Approved')
            ->pluck('title', 'id');

        return view('calculator.calculator', [
            'editing'           => true,
            'submission'        => $submission,
            'users_character'   => $users_character,
            'user_galleries'    => $user_galleries,
            'characters'        => $all_characters,
        ]);
    }

    public function getCalc()
    {
        $user = Auth::user();
        $users_character = Auth::user()->characters()->with('image')->visible()->whereNotNull('name')->whereNull('trade_id')->pluck('name', 'id')->toArray();
        $user_galleries = Auth::user()->gallerySubmissions()->where('status', 'Approved')->pluck('title', 'id');
        $all_characters = Character::with('image')  ->visible() ->whereNotNull('name') ->whereNull('trade_id') ->pluck('name', 'id') ->toArray(); 

        return view('calculator.calculator', [
            'users_character'   => $users_character,
            'user_galleries'    => $user_galleries,
            'characters'        => $all_characters,
        ]);
    }

}
