<?php

namespace App\Jobs;

use App\Models\Token;
use App\Riot\Valorant;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ObtainNewToken implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

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

        $token = $api->getToken();

        Token::firstOrCreate([
            'token' => $token->token,
            'expires_at' => $token->expiry,
        ]);
    }
}
