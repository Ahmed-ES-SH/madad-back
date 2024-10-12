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
        Schema::create('customer_reviews', function (Blueprint $table) {
            $table->id();
            $table->text('review_text'); // نص المراجعة
            $table->integer('rating'); // التقييم (مثلاً 1-5 نجوم)
            $table->date('review_date'); // تاريخ المراجعة
            $table->string('company_name')->nullable(); // اسم الشركة التي يمثلها العميل (اختياري)
            $table->boolean('approved')->default(false); // حالة الموافقة على عرض المراجعة
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->timestamps(); // تاريخ الإنشاء والتحديث
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_reviews');
    }
};
