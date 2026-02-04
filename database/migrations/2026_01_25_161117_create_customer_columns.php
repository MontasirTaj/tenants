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
        if (!Schema::hasColumn('users', 'stripe_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('stripe_id')->nullable()->index();
                $table->string('pm_type')->nullable();
                $table->string('pm_last_four', 4)->nullable();
                $table->timestamp('trial_ends_at')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('users', 'stripe_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropIndex([
                    'stripe_id',
                ]);

                $table->dropColumn(array_values(array_filter([
                    Schema::hasColumn('users', 'stripe_id') ? 'stripe_id' : null,
                    Schema::hasColumn('users', 'pm_type') ? 'pm_type' : null,
                    Schema::hasColumn('users', 'pm_last_four') ? 'pm_last_four' : null,
                    Schema::hasColumn('users', 'trial_ends_at') ? 'trial_ends_at' : null,
                ])));
            });
        }
    }
};
