<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
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

        $users = User::where('nickname', 'ilike', $term . '%')
            ->select('uid', 'nickname', 'tag')
            ->limit(15)
            ->get();

        return ShortProfile::collection($users);
    }

    /**
     * @param Request $request
     *
     * @return string
     * @throws \App\Exceptions\SoftException
     */
    public function update(Request $request)
    {
        $user = User::where('uid', $request->get('id'))
            ->firstOrFail();

        if ($user->fetched_at && $user->fetched_at->gt(Carbon::now()->subMinutes(20))) {
            $this->fail('FETCHED_RECENTLY');
        }

        UpdatePlayer::dispatch($user);

        return 'OK';
    }

    /**
     * @param Request $request
     *
     * @return PlayerProfile
     * @throws \App\Exceptions\SoftException
     */
    public function show(Request $request)
    {
        $user = $this->searchUser($request);

        return $this->getProfile($user, $request->get('page', 1));
    }

    /**
     * @param Request $request
     *
     * @return PlayerProfile
     * @throws \App\Exceptions\SoftException
     */
    public function short(Request $request)
    {
        $user = $this->searchUser($request);

        return $this->getProfile($user);
    }

    /**
     * @param Request $request
     *
     * @return User
     * @throws \App\Exceptions\SoftException
     */
    protected function searchUser(Request $request)
    {
        $tag = $request->get('tag');
        $nickname = $request->get('nickname');

        if ($tag === null || $nickname === null) {
            $this->fail('INVALID_INPUT');
        }

        $user = User::where('nickname', $nickname)
            ->where('tag', $tag)
            ->first();

        if (! $user)
        {
            $user = $this->findExact($nickname, $tag);

            if (! $user) {
                $this->fail('NOT_FOUND');
            }

            UpdatePlayer::dispatch($user);
        }

        return $user;
    }

    /**
     * @param User $user
     * @param bool $page
     *
     * @return PlayerProfile
     * @throws \App\Exceptions\SoftException
     */
    protected function getProfile(User $user, $page = false)
    {
        if (! $user->fetched_at)
        {
            UpdatePlayer::dispatch($user);

            $this->fail('NOT_FETCHED_YET');
        }

        $user->analyzeMatches();

        if (! $user->processed_at || $user->processed_at->lt($user->fetched_at)) {
            $user->analyze();
        }

        if ($page)
        {
            $matches = $user->getMatches($page);

            $user->setRelation('matches', $matches);
        }

        return new PlayerProfile($user);
    }
}
