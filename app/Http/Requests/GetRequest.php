<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GetRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'q'                   => ['string'],
            'role_id'             => ['exists:roles,id'],
            'start_date'          => ['date_format:Y-m-d'],
            'end_date'            => ['date_format:Y-m-d'],
            'gender' => ['in:male,female'],
            'size'   => ['in:small,medium,large'],
            'health_status' => ['string', 'in:healthy,injured,sick'],
            'category_id' => ['integer', 'exists:categories,id'],
            'breed_id' =>['integer', 'exists:breeds,id'],
            'user_id' =>['integer', 'exists:users,id'],
            'animal_id' =>['integer', 'exists:animals,id'],
            'with_paginate'       => ['integer', 'in:0,1'],
            'per_page'            => ['integer', 'min:1']
        ];
    }
}
