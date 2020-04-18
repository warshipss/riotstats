<?php

namespace App\Riot;

use Carbon\Carbon;
use App\Models\Token;

trait TokenFeatures
{
    /**
     * @return mixed
     */
    public function getToken()
    {
        $token = Token::where('expires_at', '>', Carbon::now()->timestamp)
            ->first();

        if (! $token && method_exists($this, 'release'))
        {
            $this->release(60);
            exit;
        }

        return $token->token;
    }
}
