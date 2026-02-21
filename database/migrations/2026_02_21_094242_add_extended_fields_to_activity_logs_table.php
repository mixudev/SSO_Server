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
        Schema::table('activity_logs', function (Blueprint $table) {
            $table->string('method', 10)->nullable()->after('action');
            $table->string('url', 500)->nullable()->after('method');
            $table->string('referer', 500)->nullable()->after('url');
            $table->string('severity', 20)->default('info')->after('referer'); // info, warning, critical
            $table->string('request_id', 64)->nullable()->after('severity');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('activity_logs', function (Blueprint $table) {
            $table->dropColumn(['method', 'url', 'referer', 'severity', 'request_id']);
        });
    }
};
