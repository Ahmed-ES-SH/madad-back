<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('question_answers', function (Blueprint $table) {
            $table->id(); // معرف فريد للسؤال
            $table->text('question'); // نص السؤال
            $table->text('answer'); // نص الجواب
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade'); // معرف المستخدم (اختياري)
            $table->boolean('is_visible')->default(true); // تحديد إذا كان السؤال يظهر للمستخدمين
            $table->timestamps(); // تاريخ الإنشاء والتحديث
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('question__answers');
    }
};
