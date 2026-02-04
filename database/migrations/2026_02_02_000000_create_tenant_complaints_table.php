<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tenant_complaints', function (Blueprint $table) {
            $table->id();
            // الربط مع جدول التينانت في قاعدة البيانات الرئيسية
            $table->unsignedBigInteger('tenant_id');
            $table->string('tenant_subdomain')->nullable();

            // بيانات مقدم البلاغ (مستخدم التينانت)
            $table->unsignedBigInteger('reporter_id')->nullable();
            $table->string('reporter_name')->nullable();
            $table->string('reporter_email')->nullable();

            // تفاصيل البلاغ
            $table->string('subject');
            $table->text('message');
            $table->string('attachment_path')->nullable();

            // حالة البلاغ
            $table->string('status')->default('open'); // open, in_progress, closed

            // رد الشركة الأم
            $table->text('admin_reply')->nullable();
            $table->unsignedBigInteger('admin_user_id')->nullable();
            $table->timestamp('admin_replied_at')->nullable();

            $table->timestamps();

            $table->index('tenant_id');
            $table->index('tenant_subdomain');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tenant_complaints');
    }
};
