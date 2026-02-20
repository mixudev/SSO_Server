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
        Schema::table('client_apps', function (Blueprint $table) {
            $table->uuid('oauth_client_id')->nullable()->after('id');
            $table->index('oauth_client_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('client_apps', function (Blueprint $table) {
            $table->dropIndex(['oauth_client_id']);
            $table->dropColumn('oauth_client_id');
        });
    }
};
