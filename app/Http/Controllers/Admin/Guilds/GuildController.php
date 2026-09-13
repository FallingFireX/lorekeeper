<?php

namespace App\Http\Controllers\Admin\Guilds;

use App\Http\Controllers\Controller;
use App\Models\Guild\Guild;
use App\Services\GuildManager;
use App\Models\User\User;
use Illuminate\Http\Request;
use Auth;
use Settings;

class GuildController extends Controller {
    /*
    |--------------------------------------------------------------------------
    | Queues Controller
    |--------------------------------------------------------------------------
    |
    | Displays information about queues as entered in the admin panel.
    | Pages displayed by this controller form the Queues section of the site.
    |
    */

    /**
     * Shows the index page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getGuildIndex(Request $request) {
        $query = Guild::query();
        $sort = $request->only(['sort']);

        if ($request->get('name')) {
            $query->where(function ($query) use ($request) {
                $query->where('guilds.name', 'LIKE', '%'.$request->get('name').'%');
            });
        }

        switch ($sort['sort'] ?? null) {
            default:
                $query->orderBy('created_at', 'DESC');
                break;
            case 'alpha':
                $query->orderBy('name');
                break;
            case 'alpha-reverse':
                $query->orderBy('name', 'DESC');
                break;
            case 'reputation':
                $query->orderBy('ranks.sort', 'DESC')->orderBy('name');
                break;
            case 'newest':
                $query->orderBy('created_at', 'DESC');
                break;
            case 'oldest':
                $query->orderBy('created_at', 'ASC');
                break;
        }

        return view('admin.guilds.index', [
            'guilds'    => $query->paginate(30)->appends($request->query()),
        ]);
    }

    /**
     * Shows the index page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getGuildQueue(Request $request) {
        //$query = GuildRequest::query();
        $data = $request->only(['sort', 'type']);
        $gq_catgory = Settings::get('guild_queue_request_category') ?? null;

        switch ($data['sort'] ?? null) {
            default:
            case 'newest':
                //$query->orderBy('created_at', 'DESC');
                break;
            case 'oldest':
                //$query->orderBy('created_at', 'ASC');
                break;
        }

        switch ($data['type'] ?? null) {
            default:
            case 'all':
                break;
            case 'creation':
                //$query->where('queue_category_id', $gq_catgory)->where('queue_type', 'new_guild');
                break;
            case 'update':
                //$query->where('queue_category_id', $gq_catgory)->where('queue_type', 'update_guild');
                break;
        }

        return view('admin.guilds.queue', [
            //'requests'    => $query->paginate(30)->appends($request->query()),
        ]);
    }

    /**
     * Edit page an individual guild.
     *
     * @param mixed $id
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getEditGuild(Request $request, $id) {
        $guild = Guild::where('id', $id)->first();

        if (!$guild) {
            abort(404);
        }

        return view('admin.guilds.guild', [
            'guild' => $guild,
            'userOptions' => User::query()->orderBy('name')->pluck('name', 'id')->toArray(),
        ]);
    }

    /**
     * create page an individual guild.
     *
     * @param mixed $id
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getCreateGuild() {

        return view('admin.guilds.guild', [
            'guild' => new Guild(),
            'users' => User::orderBy('id')->pluck('name', 'id'),
        ]);
    }

    /**
     * Edit page an individual guild.
     *
     * @param mixed $id
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function postCreateEditGuild(Request $request, GuildManager $service, $id=null) {
        $id ? $request->validate(Guild::$updateRules) : $request->validate(Guild::$createRules);
        $data = $request->only([
            'name', 'description', 'location', 
            'location', 'max_users', 'max_characters',
            'open_new_users', 
            'logo', 'remove_logo', 
        ]);

        if ($id && $service->updateGuild(Guild::find($id), $data, Auth::user())) {
            flash('Guild updated successfully.')->success();
        } elseif (!$id && $guild = $service->createGuild($data, Auth::user())) {
            flash('Guild created successfully.')->success();

            return redirect()->to('admin/guilds/edit/'.$guild->id);
        } else {
            foreach ($service->errors()->getMessages()['error'] as $error) {
                flash($error)->error();
            }
        }

        return redirect()->back();
    }

}
