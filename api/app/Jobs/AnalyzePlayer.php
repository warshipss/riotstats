<?php

namespace App\Jobs;

use Carbon\Carbon;
use App\Models\User;
use App\Riot\PlayerAnalyzer;
use Illuminate\Bus\Queueable;
use App\Features\ServiceSettings;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldQueue;

class AnalyzePlayer implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, ServiceSettings;

    /**
     * @var User
     */
    protected $user;

    /**
     * Create a new job instance.
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
        $changed = Carbon::createFromTimestamp((int) $this->getSetting('valorant.match_analyzer_changed', 0))
            ->toDateTimeString();

        $unprocessedMatches = $this->user->matches()
            ->withoutGlobalScopes()
            ->select('id', 'processed_at')
            ->where(function ($q) use ($changed) {
                $q->whereNull('processed_at')
                    ->orWhere('processed_at', '<', $changed);
            })
            ->get();

        foreach ($unprocessedMatches as $match) {
            AnalyzeMatch::dispatch($match);
        }

        if ($unprocessedMatches->count()) {
            return $this->release($unprocessedMatches->count());
        }

        $processedMatches = $this->user->matches()
            ->withoutGlobalScopes()
            ->whereNotNull('processed_at')
            ->orderBy('started_at', 'desc')
            ->select('id', 'stats', 'processed_at')
            ->where('processed_at', '>', $changed)
            ->get();

        $analyzer = new PlayerAnalyzer($this->user, $processedMatches->toArray());

        $this->user->stats = $analyzer->toArray();
        $this->user->processed_at = Carbon::now();
        $this->user->save();
    }
}
