<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('admin_roles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('description')->nullable();
            $table->timestamps();
        });

        Schema::create('admin_permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('description')->nullable();
            $table->timestamps();
        });

        Schema::create('admin_role_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('role_id')->constrained('admin_roles')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });

        Schema::create('admin_permission_role', function (Blueprint $table) {
            $table->id();
            $table->foreignId('permission_id')->constrained('admin_permissions')->cascadeOnDelete();
            $table->foreignId('role_id')->constrained('admin_roles')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admin_permission_role');
        Schema::dropIfExists('admin_role_user');
        Schema::dropIfExists('admin_permissions');
        Schema::dropIfExists('admin_roles');
    }
};
