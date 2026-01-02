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
        Schema::table('category_post', function (Blueprint $table) {
            $table->foreignId('category_id')->constrained()->onDelete('cascade')->after('id');
            $table->foreignId('post_id')->constrained()->onDelete('cascade')->after('category_id');
            $table->boolean('is_primary')->default(false)->after('post_id');
            $table->unique(['category_id', 'post_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('category_post', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropForeign(['post_id']);
            $table->dropUnique(['category_id', 'post_id']);
            $table->dropColumn(['category_id', 'post_id', 'is_primary']);
        });
    }
};
