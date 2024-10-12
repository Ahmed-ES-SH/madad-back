<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question_Answer extends Model
{
    use HasFactory;


    protected $fillable = [
        'question',
        'answer',
        'user_id',
        'is_visible',
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
