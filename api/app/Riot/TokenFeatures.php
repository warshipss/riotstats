<?php

namespace App\Riot;

use Carbon\Carbon;
use App\Models\Token;
use App\Exceptions\InvalidTokenException;

trait TokenFeatures
{
    /**
     * @return mixed
     *
     * @throws InvalidTokenException
     */
    public function getToken()
    {
        // Temporarily disabled
        return '';

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
