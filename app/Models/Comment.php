<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    // تحديد الأعمدة القابلة للتعبئة
    protected $fillable = [
        'post_id',
        'content',
        'likes',
        'author_id', // يمكنك استبداله ب 'auther_id' إذا كان ذلك مطلوبًا
    ];



    /**
     * علاقة التعليق بالمقال.
     */
    public function post()
    {
        return $this->belongsTo(BlogPost::class);
    }

    /**
     * علاقة التعليق بالمؤلف (المستخدم).
     */
    public function author()
    {
        return $this->belongsTo(User::class); // تأكد من اسم العمود هنا
    }
}
