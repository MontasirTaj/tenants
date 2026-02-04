<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::connection('tenant')->create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('guard')->nullable();
            $table->string('event')->nullable();
            $table->string('action')->nullable();
            $table->string('subject_type')->nullable();
            $table->unsignedBigInteger('subject_id')->nullable();
            $table->text('description')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->json('extra')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'created_at']);
            $table->index(['event', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::connection('tenant')->dropIfExists('activity_logs');
    }
};
