<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('plan_limits', function (Blueprint $table) {
            $table->id();
            $table->string('plan', 50)->unique();
            $table->unsignedInteger('max_users')->nullable(); // null = بدون حد
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plan_limits');
    }
};
