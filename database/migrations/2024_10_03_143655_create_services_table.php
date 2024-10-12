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
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('title'); // عنوان الخدمة
            $table->text('description'); // وصف الخدمة
            $table->json('features'); // مميزات الخدمة (مصفوفة)
            $table->decimal('expected_benefit_percentage', 5, 2); // نسبة الاستفادة المتوقعة
            $table->decimal('starting_price', 10, 2); // سعر ابتدائي للخدمة
            $table->json('images'); // صور الخدمة (مصفوفة)
            $table->foreignId('sub_category_id')->constrained('sub_categories')->onDelete('cascade'); // علاقة مع القسم الفرعي
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
