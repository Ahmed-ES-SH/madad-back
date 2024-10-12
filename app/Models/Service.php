<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'features',
        'expected_benefit_percentage',
        'starting_price',
        'images',
        'sub_category_id',
    ];

    protected $casts = [
        'features' => 'array',
        'images' => 'array',
    ];

    public function subCategory()
    {
        return $this->belongsTo(SubCategory::class);
    }
}
