<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['queued_at', 'fetched_at', 'processed_at'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'uid',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function matches()
    {
        return $this->belongsToMany(Match::class, 'match_user');
    }

    /**
     * @param int $page
     * @param int $limit
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getMatches($page = 1, $limit = 20)
    {
        $matches = $this->matches()
            ->limit($limit)
            ->offset(($page - 1) * $limit)
            ->with('users')
            ->whereNotNull('processed_at')
            ->orderBy('started_at', 'desc')
            ->get();

        $this->setRelation('matches', $matches);

        return $matches;
    }
}
