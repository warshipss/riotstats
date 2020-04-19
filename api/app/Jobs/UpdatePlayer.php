<?php

namespace App\Jobs;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Match;
use App\Riot\Valorant;
use App\Riot\TokenFeatures;
use Illuminate\Bus\Queueable;
use App\Features\ServiceSettings;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldQueue;

class UpdatePlayer implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, TokenFeatures, ServiceSettings;

    /**
     * @var User
     */
    protected $user;

    /**
     * Create a new job instance.
     *
     * @param User $user
     *
     * @return void
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->user->queued_at = Carbon::now();
        $this->user->save();

        /**
         * @var Valorant $api
         */
        $api = app(Valorant::class);

        $token = $this->getToken();

        $api->setToken($token);

        $flag = 0;
        $offset = 0;
        $perPage = 20;

        while (! $flag)
        {
            $matches = $api->getMatchHistory($this->user->uid, $offset, $offset + $perPage);

            foreach ($matches->History as $match)
            {
                $record = Match::firstOrCreate([
                    'game_id' => 1,
                    'uid' => $match->MatchID,
                ], [
                    'started_at' => Carbon::createFromTimestamp(floor($match->GameStartTime / 1e3)),
                ]);

                if ($record->wasRecentlyCreated) {
                    FetchMatch::dispatch($record);
                }
            }

            // Last page reached or no new matches
            if ($matches->EndIndex === $matches->Total || $matches->Total === $this->user->matches()->count()) {
                $flag = 1;
            }

            $offset += $perPage;

            $t = (int) $this->getSetting('valorant.timeout', 0);

            if ($t > 0) {
                usleep($t * 1e3);
            }
        }

        $this->user->queued_at = null;
        $this->user->fetched_at = Carbon::now();
        $this->user->save();
    }
}
