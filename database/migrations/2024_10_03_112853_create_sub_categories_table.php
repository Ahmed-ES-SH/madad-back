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
        Schema::create('sub_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // يجب أن تكون فريدة
            $table->text('description')->nullable(); // وصف الفئة الفرعية
            $table->string('image')->nullable(); // مسار الصورة
            $table->foreignId('category_id')->constrained('categories')->onDelete('cascade'); // الربط مع جدول الفئات
            $table->timestamps(); // تواريخ الإنشاء والتحديث
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sub_categories');
    }
};
