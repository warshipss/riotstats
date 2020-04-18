<?php

namespace App\Console\Commands;

use App\Jobs\UpdatePlayer;
use App\Models\User;
use App\Riot\TokenFeatures;
use App\Jobs\ObtainNewToken;
use Illuminate\Console\Command;

class UpdateUsers extends Command
{
    use TokenFeatures;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:users';

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
        $users = User::whereNull('fetched_at')
            ->limit(15)
            ->get();

        foreach ($users as $user) {
            UpdatePlayer::dispatch($user);
        }
    }
}
