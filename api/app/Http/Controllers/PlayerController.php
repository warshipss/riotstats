<?php

namespace App\Http\Controllers;

use App\Models\Match;
use App\Riot\Valorant;
use Carbon\Carbon;
use App\Models\User;
use App\Jobs\UpdatePlayer;
use App\Jobs\FindPlayerUid;
use Illuminate\Http\Request;
use App\Http\Resources\PlayerProfile;

class PlayerController extends Controller
{
    /**
     * @param Request $request
     *
     * @return PlayerProfile|string[]
     */
    public function show(Request $request)
    {
        $tag = $request->get('tag');
        $nickname = $request->get('nickname');

        if ($tag === null || $nickname === null) {
            abort(404);
        }

        return $this->getProfile($nickname, $tag, 1);
    }

    /**
     * @param $nickname
     * @param $tag
     * @param $page
     *
     * @return PlayerProfile|string[]
     */
    protected function getProfile($nickname, $tag, $page)
    {
        $user = User::where('nickname', trim($nickname))
            ->where('tag', trim($tag))
            ->first();

        $matches = $user->getMatches($page);

        $user->setRelation('matches', $matches);

        if (! $user)
        {
            FindPlayerUid::dispatch($nickname, $tag);

            return ['error' => 'SEARCHING_FOR_USER'];
        }

        if ($user->fetched_at->lt(Carbon::now()->subHours(12))) {
            UpdatePlayer::dispatch($user);
        }

        return new PlayerProfile($user);
    }
}
