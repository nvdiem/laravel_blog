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
        Schema::table('media', function (Blueprint $table) {
            $table->string('alt_text')->nullable()->after('file_name');
            $table->unsignedInteger('width')->nullable()->after('size');
            $table->unsignedInteger('height')->nullable()->after('width');
            
            // Indexes for filtering and sorting
            $table->index('mime_type');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('media', function (Blueprint $table) {
            $table->dropIndex(['mime_type']);
            $table->dropIndex(['created_at']);
            $table->dropColumn(['alt_text', 'width', 'height']);
        });
    }
};
