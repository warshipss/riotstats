<?php

namespace App\Jobs;

use App\Models\User;
use App\Riot\Valorant;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class FindPlayerUid implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var string
     */
    protected $tag;

    /**
     * @var string
     */
    protected $nickname;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($nickname, $tag)
    {
        $this->tag = $tag;
        $this->nickname = $nickname;
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

        $uid = $api->getUid($this->nickname, $this->tag);

        if (isset($uid->uid))
        {
            $user = User::firstOrCreate(['uid' => $uid->uid]);

            $user->tag = $this->tag;
            $user->nickname = $this->nickname;
            $user->save();
        }
    }
}
