<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tenant_complaints', function (Blueprint $table) {
            $table->timestamp('tenant_seen_at')->nullable()->after('admin_replied_at');
            $table->index('tenant_seen_at');
        });
    }

    public function down(): void
    {
        Schema::table('tenant_complaints', function (Blueprint $table) {
            $table->dropIndex(['tenant_seen_at']);
            $table->dropColumn('tenant_seen_at');
        });
    }
};
