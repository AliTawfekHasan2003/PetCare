<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactMessageRequest;
use App\Http\Resources\ContactMessageResource;
use App\Models\ContactMessage;
use Illuminate\Http\Request;

class ContactMessageController extends Controller
{

    /**
     * @OA\Post(
     * path="/contact-messages",
     * tags={"User - Contact Us"},
     * security={{"bearer_token": {} }},
     * description="Sent new message.",
     * operationId="add_message",
     *   @OA\RequestBody(
     *       required=true,
     *       @OA\MediaType(
     *           mediaType="multipart/form-data",
     *           @OA\Schema(
     *              required={"name", "email" , "message"},
     *              @OA\Property(property="name", type="string"),
     *              @OA\Property(property="email",format="email", type="string"),
     *              @OA\Property(property="message", type="string"),
     *           )
     *       )
     *   ),
     * @OA\Response(
     *     response=200,
     *     description="successful operation",
     *  ),
     *  )
     */

     public function store(ContactMessageRequest $request)
     { 
         $message = ContactMessage::create([
             'name' => $request->name,
             'email' => $request->email,
             'message' => $request->message,
         ]);
 
         return new ContactMessageResource($message);
     } 
}
