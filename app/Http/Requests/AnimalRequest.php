<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AnimalRequest extends FormRequest
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
            'name' => ['required', 'string'],
            'primary_color' =>  ['required', 'string'],
            'secondary_color' =>  ['string'],
            "weight" => ['min:0', 'numeric'],
            'address' => ['required', 'string'],
            'profile_image' => request()->isMethod('post')  ? ['required', 'image']  : ['required'],   
            'cover_image' => request()->isMethod('post')  ? ['required', 'image']  : ['required'],
            'gender' => ['required', 'in:male,female'],
            'size'   => ['required', 'in:small,medium,large'],
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'breed_id' =>['required', 'integer', 'exists:breeds,id'],
            'desc' => ['string'],
            'health_status' => ['required', 'string', 'in:healthy,injured,sick,unknown'],
            'birth_date' => ['required', 'date', 'date_format:Y-m-d', 'before_or_equal:today'],
            'attachments' => ['array'],
            'attachments.*.title' => ['required', 'string'],
            'attachments.*.file' => ['required', 'file'],
        ];
    }
}
