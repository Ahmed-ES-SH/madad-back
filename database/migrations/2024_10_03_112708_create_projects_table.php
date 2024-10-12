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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // اسم المشروع
            $table->text('description')->nullable(); // وصف المشروع
            $table->json('images')->nullable(); // مصفوفة صور المشروع
            $table->date('completion_date')->nullable(); // تاريخ الإنجاز
            $table->string('project_link')->nullable(); // رابط المشروع
            $table->string('client_name')->nullable(); // اسم العميل
            $table->string('category')->nullable(); // فئة المشروع
            $table->string('video_link')->nullable(); // رابط الفيديو
            $table->text('awards')->nullable(); // الجوائز أو الإشادات
            $table->json('technologies_used')->nullable(); // الأدوات والتقنيات المستخدمة
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
