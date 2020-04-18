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

        if (! $token)
        {
            if (method_exists($this, 'release')) {
                $this->release(60);
            }

            return false;
        }

        return $token->token;
    }
}
