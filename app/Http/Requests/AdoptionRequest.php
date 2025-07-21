<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdoptionRequest extends FormRequest
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
            'address' => ['string'],
            'family_members_count' => ['integer', 'min:1'],
            'has_children' => ['integer', 'in:0,1'],

            'children_ages' => ['required_if:has_children,1', 'array'],
            'children_ages.*' => ['integer', 'min:0'],

            'job_title' => ['string', 'max:255'],
            'company_name' => [ 'string', 'max:255'],
            'work_hours_per_day' => ['integer', 'min:0'],
            'work_type' => ['in:remote,on_site,hybrid'],

            'housing_type' => ['in:apartment,house,villa'],
            'is_rented' => ['integer', 'in:0,1'],

            'landlord_name' => ['required_if:is_rented,1', 'string', 'max:255'],
            'landlord_phone' => ['required_if:is_rented,1', 'string'],
            'landlord_allows_pets' => ['required_if:is_rented,1', 'integer', 'in:0,1'],

            'has_garden' => ['integer', 'in:0,1'],

            'has_patience' => ['integer', 'in:0,1'],
            'can_handle_issues' => ['integer', 'in:0,1'],
            'hours_with_pet_daily' => ['integer', 'min:0'],
            'someone_home_24_7' => ['integer', 'in:0,1'],
            'can_be_with_pet_when_sick' => ['integer', 'in:0,1'],

            'agreed_to_terms' => ['integer', 'in:0,1'],
            'notes' => [ 'string'],
        ];
    }
}
