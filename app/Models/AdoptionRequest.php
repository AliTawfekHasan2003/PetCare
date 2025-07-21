<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdoptionRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'animal_id',
        'user_id',
        'status',
        'address',
        'family_members_count',
        'has_children',
        'children_ages',
        'job_title',
        'company_name',
        'work_hours_per_day',
        'work_type',
        'housing_type',
        'is_rented',
        'landlord_name',
        'landlord_phone',
        'landlord_allows_pets',
        'has_garden',
        'has_patience',
        'can_handle_issues',
        'hours_with_pet_daily',
        'someone_home_24_7',
        'can_be_with_pet_when_sick',
        'agreed_to_terms',
        'notes',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function animal()
    {
        return $this->belongsTo(Animal::class);
    }

}
