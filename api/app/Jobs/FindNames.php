<?php

namespace App\Jobs;

use App\Models\User;
use App\Riot\Valorant;
use App\Riot\TokenFeatures;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class FindNames implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, TokenFeatures;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
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

        $unknown = User::whereNull('nickname')
            ->whereNull('tag')
            ->get();

        if (! $unknown->count()) {
            return;
        }

        $response = $api->getNames($unknown->pluck('uid'));

        foreach ($response as $user)
        {
            User::where('uid', '=', $user->Subject)
                ->update([
                    'tag' => $user->TagLine,
                    'nickname' => $user->GameName,
                ]);
        }
    }
}
