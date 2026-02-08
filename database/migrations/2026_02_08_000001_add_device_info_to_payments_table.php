<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            if (!Schema::hasColumn('payments', 'ip_address')) {
                $table->string('ip_address', 45)->nullable()->after('type');
            }
            if (!Schema::hasColumn('payments', 'user_agent')) {
                $table->text('user_agent')->nullable()->after('ip_address');
            }
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            if (Schema::hasColumn('payments', 'user_agent')) {
                $table->dropColumn('user_agent');
            }
            if (Schema::hasColumn('payments', 'ip_address')) {
                $table->dropColumn('ip_address');
            }
        });
    }
};
