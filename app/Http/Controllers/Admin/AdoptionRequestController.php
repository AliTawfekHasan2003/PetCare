<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\GetRequest;
use App\Http\Resources\AdoptionRequestResource;
use App\Http\Resources\AnimalResource;
use App\Models\AdoptionRequest;
use Illuminate\Http\Request;

class AdoptionRequestController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
        $this->middleware('role:admin');
    }

    /**
     * @OA\Get(
     *   path="/admin/adoption-requests",
     *   description="Get all adoption requests",
     *   operationId="get_adoption_requests",
     *   tags={"Admin - Adoption Requests"},
     *   security={{"bearer_token": {} }},
     *   @OA\Parameter(
     *     in="query",
     *     name="user_id",
     *     required=false,
     *     @OA\Schema(type="integer"),
     *   ),
     *   @OA\Parameter(
     *     in="query",
     *     name="animal_id",
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

    public function index(GetRequest $request)
    {
        $q = AdoptionRequest::query()->with(['user', 'animal', 'animal.category', 'animal.breed', 'animal.attachments'])->latest();

        if($request->status)
            $q->where('status', $request->status);

        if($request->user_id){
            $q->where('user_id', $request->user_id);
        }

        if($request->animal_id){
            $q->where('animal_id', $request->animal_id);
        }

        if ($request->with_paginate === '0')
            $adoption_requests = $q->get();
        else
            $adoption_requests = $q->paginate($request->per_page ?? 10);

        return AdoptionRequestResource::collection($adoption_requests);
    }

    /**
     * @OA\Get(
     *   path="/admin/adoption-requests/{id}",
     *   description="Get specific pending adoption request",
     *   @OA\Parameter(
     *     in="path",
     *     name="id",
     *     required=true,
     *     @OA\Schema(type="string"),
     *   ),
     *   operationId="show_pending_adoption_request",
     *   security={{"bearer_token": {} }},
     *   tags={"Admin - Adoption Requests"},
     *   @OA\Response(
     *     response=200,
     *     description="Success"
     *   ),
     * )
     */
    public function show(AdoptionRequest $adoption_request)
    {   
        $adoption_request->load(['user', 'animal', 'animal.category', 'animal.breed', 'animal.attachments']);

        return response()->json(new AdoptionRequestResource($adoption_request));
    }

    /**
     * @OA\post(
     *   path="/admin/pending-adoption-requests/{id}/change-adoption-request-status",
     *   description="accept or reject adoption requests",
     *   operationId="change_adoption_request_status",
     *   tags={"Admin - Adoption Requests"},
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

    public function change_adoption_request_status(AdoptionRequest $adoption_request, Request $request)
    {
        $request->validate(['new_status' => ['required', 'in:accepted,rejected']]);

        if($adoption_request->status != 'pending')  
            return response()->json(['this adoption request is not pending'], 400);
        
        $animal = $adoption_request->animal()->whereHas('adoption_requests', function($query){
            return $query->where('status', 'accepted');
        })->first();

        if($animal)
           return response()->json(['this animal already has been adopted'], 400);



        $adoption_request->status = $request->new_status;
        $adoption_request->save();

        return response()->json(new AdoptionRequestResource($adoption_request));
    }
}
