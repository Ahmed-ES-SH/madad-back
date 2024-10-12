<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;


    protected $fiilable = [
        "name",
        "description",
        "images",
        "completion_date",
        "project_link",
        "client_name",
        "category",
        "video_link",
        "awards",
        "technologies_used",
    ];

    protected $casts = [
        'images' => 'array', // تحويل images إلى مصفوفة
        'technologies_used' => 'array', // تحويل technologies_used إلى مصفوفة
    ];
}
