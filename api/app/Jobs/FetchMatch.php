<?php

namespace App\Jobs;

use App\Models\User;
use App\Models\Match;
use App\Riot\Valorant;
use App\Riot\TokenFeatures;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use App\Features\ServiceSettings;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldQueue;

class FetchMatch implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, TokenFeatures, ServiceSettings;

    /**
     * @var Match
     */
    protected $match;

    /**
     * @var bool
     */
    protected $flagNewUsers = false;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Match $match)
    {
        $this->match = $match;
    }

    /**
     * Get the tags that should be assigned to the job.
     *
     * @return array
     */
    public function tags()
    {
        return ['fetch', 'match:' . $this->match->id];
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        /**
         * @var Valorant $api
         */
        $api = app(Valorant::class);

        $token = $this->getToken();

        $api->setToken($token);

        $response = $api->getMatchDetails($this->match->uid);

        $ids = $this->getInternalIds($response->players);

        $this->match->original = json_encode($response);
        $this->match->save();

        $this->match->users()->sync(array_values($ids));
        $this->match->users()->update([
            'fetched_at' => Carbon::now(),
        ]);

        $this->match->analyze();

        if ($this->flagNewUsers) {
            FindNames::dispatch();
        }

        $t = (int) $this->getSetting('valorant.timeout', 0);

        if ($t > 0) {
            usleep($t * 1e3);
        }
    }

    /**
     * @param $uids
     *
     * @return array
     */
    protected function getInternalIds($players)
    {
        $result = [];
        $uids = array_map(function ($player) {
            return $player->subject;
        }, $players);

        foreach ($uids as $uid)
        {
            $user = User::where('uid', $uid)
                ->first();

            if (! $user) {
                $user = User::create(compact('uid'));
            }

            if ($user->wasRecentlyCreated) {
                $this->flagNewUsers = true;
            }

            $result[] = $user->id;
        }

        return array_combine($uids, $result);
    }
}
