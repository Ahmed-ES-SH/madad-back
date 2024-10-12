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
        Schema::create('team_members', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('image')->nullable(); // حقل لصورة العضو
            $table->text('description'); // حقل لوصف العضو
            $table->string('position'); // حقل للمركز داخل الشركة
            $table->string('facebook')->nullable(); // رابط فيسبوك
            $table->string('X_Account')->nullable(); // رابط تويتر
            $table->string('instagram')->nullable(); // رابط إنستغرام
            $table->timestamps(); // حقول تاريخ الإنشاء والتحديث
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('team_members');
    }
};
