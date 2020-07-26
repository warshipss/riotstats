<?php

namespace App\Jobs;

use App\Blitz\MatchNormalize;
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

class UpdatePlayerBlitz implements ShouldQueue
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
     * Get the tags that should be assigned to the job.
     *
     * @return array
     */
    public function tags()
    {
        return ['fetch', 'user:' . $this->user->id];
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

        $flag = 0;
        $offset = 0;
        $perPage = 20;

        $count = $this->user->matches()->count();

        while (! $flag)
        {
            try {
                $matches = $api->getMatchHistory($this->user->uid, $offset, $offset + $perPage);
            } catch (\Exception $e) {
                $this->user->fetched_at = Carbon::now();
                $this->user->save();

                break;
            }

            foreach ($matches->data as $match)
            {
                $record = Match::where('uid', $match->id)
                    ->where('game_id', 1)
                    ->first();

                if (! $record)
                {
                    $record = Match::create([
                        'game_id' => 1,
                        'uid' => $match->id,
                        'original' => MatchNormalize::handle($match),
                        'started_at' => Carbon::createFromTimeString($match->startedAt),
                    ]);

                    $ids = $this->getInternalIds($match->players);

                    $record->users()->sync(array_values($ids));
                    $record->users()->update([
                        'fetched_at' => Carbon::now(),
                    ]);
                }
            }

            // Last page reached or no new matches
            if ($matches->limit + $matches->offset >= $matches->count) {
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

        $this->user->analyze();

        cache()->delete($this->user->getCacheKey());
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
