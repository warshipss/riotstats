<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Jobs\AnalyzeMatch;
use App\Jobs\UpdatePlayer;
use Illuminate\Http\Request;
use App\Features\PlayerProcessing;
use App\Http\Resources\ShortProfile;
use App\Http\Resources\PlayerProfile;

class PlayerController extends Controller
{
    use PlayerProcessing;

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function search(Request $request)
    {
        $term = $request->get('term');

        $users = User::where('nickname', 'like', $term . '%')
            ->select('uid', 'nickname', 'tag')
            ->limit(15)
            ->get();

        return ShortProfile::collection($users);
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function update(Request $request)
    {
        $term = $request->get('term');

        $users = User::where('nickname', 'like', $term . '%')
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
        $user = $this->searchUser($request);

        return $this->getProfile($user, 1);
    }

    /**
     * @param Request $request
     *
     * @return PlayerProfile|string[]
     */
    public function short(Request $request)
    {
        $user = $this->searchUser($request);

        return $this->getProfile($user);
    }

    /**
     * @param Request $request
     *
     * @return mixed
     */
    protected function searchUser(Request $request)
    {
        $tag = $request->get('tag');
        $nickname = $request->get('nickname');

        if ($tag === null || $nickname === null) {
            abort(406, 'INVALID_INPUT');
        }

        $user = User::where('nickname', $nickname)
            ->where('tag', $tag)
            ->first();

        if (! $user)
        {
            $user = $this->findExact($nickname, $tag);

            if (! $user) {
                abort(404, 'NOT_FOUND');
            }

            UpdatePlayer::dispatch($user);
        }

        return $user;
    }

    /**
     * @param User $user
     * @param bool $page
     *
     * @return PlayerProfile|string[]
     */
    protected function getProfile(User $user, $page = false)
    {
        if (! $user->fetched_at)
        {
            UpdatePlayer::dispatch($user);

            return ['error' => 'NOT_FETCHED_YET'];
        }

        $user->analyzeMatches();

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
