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
        Schema::create('attachments', function (Blueprint $table) {
            $table->id();

            // نوع المرفق (ملف / صورة)
            $table->string('type')->comment('file or image');

            // الاسم الأصلي للمرفق
            $table->string('original_name');

            // امتداد الملف (pdf, jpg, ...)
            $table->string('extension', 20)->nullable();

            // نوع المحتوى MIME
            $table->string('mime_type', 100)->nullable();

            // المسار داخل التخزين (storage path)
            $table->string('path');

            // القرص المستخدم (public, s3, ...)
            $table->string('disk')->default('public');

            // الحجم بالبايت
            $table->unsignedBigInteger('size_bytes')->nullable();

            // عدد الصفحات (إن كان ملفاً يدعم ذلك)، يسمح بالقيمة الفارغة في البداية
            $table->unsignedInteger('page_count')->nullable();

            // المستخدم الذي أرفق الملف (من جدول users في قاعدة المستأجر)
            $table->foreignId('uploaded_by')->constrained('users');

            // حقل استجابة المعالجة (JSON أو نص طويل)، فارغ في البداية
            $table->json('processing_response')->nullable();

            // تاريخ/وقت المعالجة (nullable في البداية)
            $table->timestamp('processed_at')->nullable();

            // المستخدم الذي عالج المرفق (nullable في البداية)
            $table->foreignId('processed_by')->nullable()->constrained('users');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attachments');
    }
};
