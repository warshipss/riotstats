<?php

namespace App\Console\Commands\Tokens;

use Carbon\Carbon;
use App\Models\Token;
use Illuminate\Console\Command;

class Clear extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tokens:clear';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Deletes expired tokens';

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
        Token::where('expires_at', '<', Carbon::now()->subHour()->timestamp)
            ->delete();
    }
}
