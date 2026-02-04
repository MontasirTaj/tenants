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
        Schema::create('tenants', function (Blueprint $table) {
            $table->bigIncrements('TenantID');

            // ...existing code...
            $table->string('TenantName')->index();
            $table->string('OwnerName')->nullable()->index();
            $table->string('PhoneNumber', 20)->nullable()->index();
            $table->string('Subdomain')->unique();
            $table->string('DBName')->unique();
            $table->string('DBHost')->nullable();
            $table->string('DBUser')->nullable();
            $table->string('DBPassword')->nullable();
            $table->string('DBPort')->nullable();
            $table->string('Email')->nullable()->index();
            $table->string('Address')->nullable();
            $table->string('Logo')->nullable();

            $table->string('Plan', 32)->default('free')->index();
            $table->date('JoinDate')->index();
            $table->date('SubscriptionStartDate')->nullable();
            $table->date('SubscriptionEndDate')->nullable()->index();
            $table->date('TrialEndDate')->nullable();

            $table->boolean('IsActive')->default(true)->index();

            $table->text('Notes')->nullable();
            $table->tinyInteger('Status')->default(1)->index();
            $table->unsignedBigInteger('CUserID')->nullable()->index();
            $table->dateTime('CDate')->nullable();
            $table->unsignedBigInteger('UUserID')->nullable()->index();
            $table->dateTime('UDate')->nullable();
            $table->unsignedBigInteger('DUserID')->nullable()->index();
            $table->dateTime('DDate')->nullable();
            $table->index(['IsActive', 'Plan']);
            $table->index(['TenantName', 'Subdomain']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tenants');
    }
};
