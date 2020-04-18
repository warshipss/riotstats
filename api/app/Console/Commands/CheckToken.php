<?php

namespace App\Console\Commands;

use App\Models\Token;
use App\Riot\Valorant;
use App\Riot\TokenFeatures;
use Illuminate\Console\Command;

class CheckToken extends Command
{
    use TokenFeatures;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:token';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $token = $this->getToken();

        // Return if token exists
        if ($token) {
            return true;
        }

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
