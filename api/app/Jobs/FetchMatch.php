<?php

namespace App\Jobs;

use App\Models\User;
use App\Models\Match;
use App\Riot\Valorant;
use App\Riot\TokenFeatures;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldQueue;

class FetchMatch implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, TokenFeatures;

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
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        usleep(500e3);

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

        AnalyzeMatch::dispatch($this->match);

        if ($this->flagNewUsers) {
            FindNames::dispatch();
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
            $user = User::firstOrCreate(compact('uid'));

            if ($user->wasRecentlyCreated) {
                $this->flagNewUsers = true;
            }

            $result[] = $user->id;
        }

        return array_combine($uids, $result);
    }
}
