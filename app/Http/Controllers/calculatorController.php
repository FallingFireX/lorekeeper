<?php

namespace App\Http\Controllers;

use App\Models\ArtTracker;
use Illuminate\Routing\Controller as BaseController;
use App\Models\Character\Character;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Arr;

class calculatorController extends BaseController {

    public function postCreateEditTracker (Request $request, $id = null){
        if (!$id) {
            $id = $request->route('id');
        }

        if ($id) {
            $tracker = ArtTracker::find($id);
            
            // Block edit if not pending
            if ($tracker->status !== 'Pending') {
                flash('This tracker can no longer be edited.')->error();
                return redirect()->back();
            }
            
            $request->validate(ArtTracker::$updateRules);
            $tracker->update($request->only(['url', 'external_url', 'total', 'notes', 'character_id']));
            flash('Submission updated successfully.')->success();
        } 
        else {
            $request->validate(ArtTracker::$createRules);
            $data = $request->only(['url', 'external_url', 'total', 'notes', 'character_id']);
            $data['user_id'] = Auth::id();
            $data['status'] = 'pending'; // Set initial status
            
            $tracker = ArtTracker::create($data);
            flash('Submission created successfully.')->success();
            return redirect()->to('fp/calculator/edit/'.$tracker->id);
        }

        return redirect()->back();
    }
    
    

    /**
     * @param int $id
     */
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
            'tracker'           => new ArtTracker,
        ]);
    }

    /**
     * @param int $id
     */
    public function getEditCalc($id)
    {
       
        $tracker = ArtTracker::find($id);$user = Auth::user();
        $users_character = Auth::user()->characters()->with('image')->visible()->whereNotNull('name')->whereNull('trade_id')->pluck('name', 'id')->toArray();
        $user_galleries = Auth::user()->gallerySubmissions()->where('status', 'Approved')->pluck('title', 'id');
        $all_characters = Character::with('image')  ->visible() ->whereNotNull('name') ->whereNull('trade_id') ->pluck('name', 'id') ->toArray(); 
        $submission = ArtTracker::findOrFail($id);

        return view('calculator.calculator', [
            'submission' => $submission,
            'users_character'   => $users_character,
            'user_galleries'    => $user_galleries,
            'characters'        => $all_characters,
            'tracker'           => $tracker,
        ]);
    }

   

}
