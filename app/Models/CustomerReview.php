<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerReview extends Model
{
    use HasFactory;

    protected $fillable = [
        'review_text',
        'rating',
        'review_date',
        'company_name',
        'approved',
        'user_id',
    ];


    // العلاقة مع User
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
