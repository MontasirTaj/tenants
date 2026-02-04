<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('payments')) {
            Schema::create('payments', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('tenant_id')->nullable();
                $table->foreign('tenant_id')->references('TenantID')->on('tenants')->onDelete('set null');

                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();

                $table->string('plan')->nullable();
                $table->string('currency', 10)->nullable();
                $table->integer('amount_total')->nullable();
                $table->string('status', 50)->nullable();
                $table->string('type', 50)->default('tenant_signup');

                $table->string('stripe_session_id')->nullable()->index();
                $table->string('stripe_payment_intent_id')->nullable()->index();
                $table->string('stripe_customer_id')->nullable()->index();
                $table->string('stripe_charge_id')->nullable()->index();
                $table->string('receipt_url')->nullable();

                $table->json('customer_details')->nullable();
                $table->json('metadata')->nullable();

                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
