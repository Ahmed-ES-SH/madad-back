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
        Schema::create('company_information', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // اسم الشركة
            $table->text('vision'); // رؤية الشركة
            $table->text('goals'); // أهداف الشركة
            $table->text('values'); // قيم الشركة
            $table->string('address')->nullable(); // عنوان الشركة (اختياري)
            $table->string('phone_number')->nullable(); // رقم الهاتف (اختياري)
            $table->string('vision_image')->nullable(); // مسار صورة الرؤية
            $table->string('goals_image')->nullable(); // مسار صورة الأهداف
            $table->string('values_image')->nullable(); // مسار صورة القيم
            $table->timestamps(); // تسجيل تاريخ الإنشاء والتحديث
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_information');
    }
};
