<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\GetRequest;
use App\Http\Resources\ContactMessageResource;
use App\Models\ContactMessage;
use Illuminate\Http\Request;

class ContactMessageController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
        $this->middleware('role:admin');
    }

    /**
     * @OA\Get(
     *   path="/admin/contact-messages",
     *   description="Get all  messages",
     *   operationId="get_all_messages",
     *   tags={"Admin - Contact Us"},
     *   security={{"bearer_token": {} }},
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
        $q = ContactMessage::query()->latest();

        if ($request->with_paginate === '0')
            $messages = $q->get();
        else
            $messages = $q->paginate($request->per_page ?? 10);

        return ContactMessageResource::collection($messages);
    }
}
