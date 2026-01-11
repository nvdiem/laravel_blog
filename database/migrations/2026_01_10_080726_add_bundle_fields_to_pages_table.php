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
        Schema::table('pages', function (Blueprint $table) {
            $table->string('bundle_disk')->nullable()->after('storage_path');
            $table->string('bundle_path')->nullable()->after('bundle_disk');
            $table->string('bundle_version')->nullable()->after('bundle_path');

            // Index for performance
            $table->index(['bundle_disk', 'bundle_path']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pages', function (Blueprint $table) {
            $table->dropIndex(['bundle_disk', 'bundle_path']);
            $table->dropColumn(['bundle_disk', 'bundle_path', 'bundle_version']);
        });
    }
};
