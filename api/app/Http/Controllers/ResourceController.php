<?php

namespace App\Http\Controllers;

use App\Models\Resource;
use Illuminate\Http\Request;
use App\Http\Resources\PlayerProfile;
use App\Http\Resources\Resource as HttpResource;

class ResourceController extends Controller
{
    /**
     * @return mixed
     * @throws \Exception
     */
    public function index()
    {
        return cache()->remember('resources', 60, function () {
            return HttpResource::collection(Resource::all());
        });
    }
}
