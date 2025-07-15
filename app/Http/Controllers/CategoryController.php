<?php

namespace App\Http\Controllers;

use App\Http\Requests\GetRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
     /**
     * @OA\Get(  
     *   path="/categories",
     *   description="Get all categories",
     *   operationId="get_all_categories",
     *   tags={"User - Categories"},
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
        $q = Category::query();


        if ($request->with_paginate === '0')
            $category = $q->get();
        else
            $category = $q->paginate($request->per_page ?? 10);

        return CategoryResource::collection($category);
    }
}
