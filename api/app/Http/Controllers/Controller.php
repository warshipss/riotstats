<?php

namespace App\Http\Controllers;

use App\Exceptions\SoftException;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * @param $message
     * @param bool $soft
     *
     * @throws SoftException
     */
    public function fail($message, $soft = true)
    {
        if ($soft) {
            throw new SoftException($message);
        }

        throw new \RuntimeException($message);
    }
}
