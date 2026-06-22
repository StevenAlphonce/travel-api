<?php

namespace Admin;

use App\Http\Requests\TravelRequest;
use App\Http\Resources\TravelResource;
use App\Models\Travel;

class TravelController
{
    public function store(TravelRequest $request){

        $travel =Travel::create($request->validated());

        return new TravelResource($travel);
    }
}
