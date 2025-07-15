<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Animal extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'category_id',
        'breed_id',
        'status',
        'primary_color',
        'secondary_color',
        'weight',
        'address',
        'profile_image',
        'cover_image',
        'gender',
        'size', 
        'desc',
        'health_status',
        'birth_date', 
];

  public function user()
  {
     return $this->belongsTo(User::class);
  }

  public function category()
  {
     return $this->belongsTo(Category::class);
  }

  public function breed()
  {
     return $this->belongsTo(Breed::class);
  }

  public  function attachments()
  {
    return $this->hasMany(Attachment::class);
  }
}
