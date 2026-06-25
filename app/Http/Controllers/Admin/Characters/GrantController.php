<?php

namespace App\Http\Controllers\Admin\Characters;

use App\Http\Controllers\Controller;
use App\Models\Character\Character;
use App\Models\Currency\Currency;
use App\Services\CurrencyManager;
use App\Services\InventoryManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GrantController extends Controller {
    /**
     * Grants or removes currency from a character.
     *
     * @param string                       $slug
     * @param App\Services\CurrencyManager $service
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postCharacterCurrency($slug, Request $request, CurrencyManager $service) {
        $data = $request->only(['currency_id', 'quantity', 'data']);
        if ($service->grantCharacterCurrencies($data, Character::where('slug', $slug)->first(), Auth::user())) {
            flash('Currency granted successfully.')->success();
        } else {
            foreach ($service->errors()->getMessages()['error'] as $error) {
                flash($error)->error();
            }
        }

        return redirect()->back();
    }

    /**
     * Grants items to characters.
     *
     * @param string                        $slug
     * @param App\Services\InventoryManager $service
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postCharacterItems($slug, Request $request, InventoryManager $service) {
        $data = $request->only(['item_ids', 'quantities', 'data', 'disallow_transfer', 'notes']);
        if ($service->grantCharacterItems($data, Character::where('slug', $slug)->first(), Auth::user())) {
            flash('Items granted successfully.')->success();
        } else {
            foreach ($service->errors()->getMessages()['error'] as $error) {
                flash($error)->error();
            }
        }

        return redirect()->back();
    }

    /**
     * Grants or removes EXP from a character.
     *
     * @param string                       $slug
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postCharacterDP($slug, Request $request) {
        $validated = $request->validate([
            'quantity' => 'required|integer',
        ]);

        $character = Character::where('slug', $slug)->first();

        if (!$character) {
            flash('Character not found.')->error();
            return redirect()->back();
        }

        $quantity = $request->integer('quantity');

       
        DB::transaction(function () use ($character, $request, $quantity) {
            $character->increment('total_fp', $quantity);

            $character->refresh();
            $character->updateRank();

            FpLog::create([
                'character_id' => $character->id,
                'admin_id'     => Auth::id(),
                'amount'       => $quantity,
                'reason'       => $request->input('data'), 
                'source'       => 'Admin Grant',           
            ]);

            ArtTracker::create([
                'character_id' => $character->id,
                'status'       => 'Approved',
                'staff_id'        => Auth::id(),
                'data' => [
                    'art_type' => 'Admin Grant',
                    'total'    => $quantity,
                ],
            ]);

            $staff = Auth::user(); 
            Notifications::create('FP_GRANT', $character->user, [
                'staff_url'         => $staff->url,
                'staff_name'        => $staff->name,
                'character_url'     => $character->url,
                'character_name'    => $character->fullName,
                'quantity'          => $quantity,
            ]);
                    
        });

        flash('FP Granted and logged successfully!')->success();
        return redirect()->back();
    }
}
