<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\GetRequest;
use App\Http\Resources\AnimalResource;
use App\Models\Animal;
use Illuminate\Http\Request;

class AnimalController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
        $this->middleware('role:admin');
    }

    /**
     * @OA\Get(
     *   path="/admin/animals",
     *   description="Get all animals",
     *   operationId="get_animals_for_admin",
     *   tags={"Admin - Animals"},
     *   security={{"bearer_token": {} }},
     *   @OA\Parameter(
     *     in="query",
     *     name="category_id",
     *     required=false,
     *     @OA\Schema(type="integer"),
     *   ),
     *   @OA\Parameter(
     *     in="query",
     *     name="breed_id",
     *     required=false,
     *     @OA\Schema(type="integer"),
     *   ),
     *  @OA\Parameter(
     *     in="query",
     *     name="gender",
     *     required=false,
     *     @OA\Schema(type="string",enum={"male", "female"})
     *   ),
     *  @OA\Parameter(
     *     in="query",
     *     name="size",
     *     required=false,
     *     @OA\Schema(type="string",enum={"small", "medium", "large"})
     *   ),
     *  @OA\Parameter(
     *     in="query",
     *     name="health_status",
     *     required=false,
     *     @OA\Schema(type="string",enum={"healthy" ,"injured" ,"sick"})
     *   ),
     *   @OA\Parameter(
     *     in="query",
     *     name="per_page",
     *     required=false,
     *     @OA\Schema(type="integer"),
     *   ),
     *   @OA\Parameter(
     *     in="query",
     *     name="status",
     *     required=false,
     *     @OA\Schema(type="string",  enum={"pending", "accepted", "rejected"}),
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
     
    public function index (GetRequest $request)
    {
        $q = Animal::query()->with(['category', 'breed', 'attachments', 'user'])->latest();

        if($request->status)
           $q->where('status', $request->status);

        if($request->category_id){
            $q->where('category_id', $request->category_id);
        }

        if($request->breed_id){
            $q->where('breed_id', $request->breed_id);
        }
        if($request->gender){
            $q->where('gender', $request->gender);
        }

        if($request->size){
            $q->where('size', $request->size);
        }

        if($request->health_status){
            $q->where('health_status', $request->health_status);
        }

        if ($request->with_paginate === '0')
            $animal = $q->get();
        else
            $animal = $q->paginate($request->per_page ?? 10);

        return AnimalResource::collection($animal);
    }

     /**
     * @OA\Get(
     *   path="/admin/animals/{id}",
     *   description="Get specific pending animal",
     *   @OA\Parameter(
     *     in="path",
     *     name="id",
     *     required=true,
     *     @OA\Schema(type="string"),
     *   ),
     *   operationId="show_animal_for_admin",
     *   tags={"Admin - Animals"},
     *   security={{"bearer_token": {} }},
     *   @OA\Response(
     *     response=200,
     *     description="Success"
     *   ),
     * )
     */
    public function show(Animal $animal)
    {   

        $animal->load(['category', 'breed', 'attachments', 'user']);

        return response()->json(new AnimalResource($animal));
    }

    /**
     * @OA\post(
     *   path="/admin/pending-animals/{id}/change-animal-status",
     *   description="accept or reject pending animal",
     *   operationId="change_animal_status",
     *   tags={"Admin - Animals"},
     *   security={{"bearer_token": {} }},
     *   @OA\Parameter(
     *     in="path",
     *     name="id",
     *     required=true,
     *     @OA\Schema(type="string"),
     *   ),
     *   @OA\RequestBody(
     *       required=true,
     *       @OA\MediaType(
     *           mediaType="multipart/form-data",
     *           @OA\Schema(
     *              required={"new_status"},
     *              @OA\Property(property="new_status", type="string", enum={"accepted", "rejected"}),
     *           )
     *       )
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Success",
     *   ),
     * )
    */
     
    public function change_animal_status(Animal $animal, Request $request)
    {
        $request->validate(['new_status' => ['required', 'in:accepted,rejected']]);

        if($animal->status != 'pending')  
           return response()->json(['this animal is not pending'], 400);

        $animal->status = $request->new_status;
        $animal->save();

        return response()->json(new AnimalResource($animal));
    }
}
