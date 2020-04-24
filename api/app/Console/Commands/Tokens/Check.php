<?php

namespace App\Console\Commands\Tokens;

use App\Models\Token;
use App\Riot\Valorant;
use App\Riot\TokenFeatures;
use Illuminate\Console\Command;
use App\Exceptions\InvalidTokenException;

class Check extends Command
{
    use TokenFeatures;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tokens:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Obtains new tokens when needed';

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
     * @return void
     *
     * @throws \App\Exceptions\InvalidTokenException
     */
    public function handle()
    {
        try {
            $this->getToken();
        } catch (InvalidTokenException $e) {
            $this->obtainNewToken();
        }
    }

    /**
     * @return void
     */
    protected function obtainNewToken()
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
