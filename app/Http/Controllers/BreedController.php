<?php

namespace App\Http\Controllers;

use App\Http\Requests\GetRequest;
use App\Http\Resources\BreedResource;
use App\Models\Breed;
use Illuminate\Http\Request;

class BreedController extends Controller
{
    /**
     * @OA\Get(
     *   path="/breeds",
     *   description="Get all breeds",
     *   operationId="get_all_breeds",
     *   tags={"User - Breeds"},
     *   @OA\Parameter(
     *     in="query",
     *     name="category_id",
     *     required=false,
     *     @OA\Schema(type="integer"),
     *   ),
     *   @OA\Parameter(
     *     in="query",
     *     name="per_page",
     *     required=false,
     *     @OA\Schema(type="integer"),
     *   ),
     *   @OA\Parameter(
     *     in="query",
     *     name="with_paginate",
     *     required=false,
     *     @OA\Schema(type="integer",enum={0, 1})
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Success",
     *   ),
     * )
    */
     
    public function index(GetRequest $request)
    {
        $q = Breed::query()->with(['category']);

        if($request->category_id){
            $q->where('category_id', $request->category_id);
        }

        if ($request->with_paginate === '0')
            $breed = $q->get();
        else
            $breed = $q->paginate($request->per_page ?? 10);

        return BreedResource::collection($breed);
    }
}
