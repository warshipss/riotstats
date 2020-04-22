<?php

namespace App\Http\Controllers;

use App\Jobs\AnalyzeMatch;
use Carbon\Carbon;
use App\Models\User;
use App\Jobs\UpdatePlayer;
use App\Jobs\FindPlayerUid;
use Illuminate\Http\Request;
use App\Features\ServiceSettings;
use App\Http\Resources\ShortProfile;
use App\Http\Resources\PlayerProfile;

class PlayerController extends Controller
{
    use ServiceSettings;

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function search(Request $request)
    {
        $term = $request->get('term');

        $users = User::where('nickname', 'like', $term . '%')
            ->withoutGlobalScopes()
            ->select('uid', 'nickname', 'tag')
            ->limit(15)
            ->get();

        return ShortProfile::collection($users);
    }

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
     * @param Request $request
     *
     * @return PlayerProfile|string[]
     */
    public function short(Request $request)
    {
        $tag = $request->get('tag');
        $nickname = $request->get('nickname');

        if ($tag === null || $nickname === null) {
            abort(404);
        }

        return $this->getProfile($nickname, $tag);
    }

    /**
     * @param $nickname
     * @param $tag
     * @param $page
     *
     * @return PlayerProfile|string[]
     */
    protected function getProfile($nickname, $tag, $page = false)
    {
        $user = User::where('nickname', $nickname)
            ->where('tag', $tag)
            ->first();

        if (! $user)
        {
            FindPlayerUid::dispatch($nickname, $tag);

            return ['error' => 'SEARCHING_FOR_USER'];
        }

        if (! $user->fetched_at)
        {
            UpdatePlayer::dispatch($user);

            return ['error' => 'NOT_FETCHED_YET'];
        }

        $unprocessed = $user->getUnprocessedMatches();

        foreach ($unprocessed as $match) {
            AnalyzeMatch::dispatch($match);
        }

        if (! $user->processed_at || $user->processed_at->lt($user->fetched_at)) {
            $user->process();
        }

        if ($page)
        {
            $matches = $user->getMatches($page);

            $user->setRelation('matches', $matches);
        }

        return new PlayerProfile($user);
    }
}
