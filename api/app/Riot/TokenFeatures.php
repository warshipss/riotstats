<?php

namespace App\Riot;

use App\Exceptions\InvalidTokenException;
use Carbon\Carbon;
use App\Models\Token;

trait TokenFeatures
{
    /**
     * @return mixed
     *
     * @throws InvalidTokenException
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

            throw new InvalidTokenException;
        }

        return $token->token;
    }
}
