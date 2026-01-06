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
        Schema::table('posts', function (Blueprint $table) {
            // We need to use raw SQL to modify enum in MySQL effectively or use DB::statement
            // Since Doctrine DBAL enum support can be tricky with Laravel migrations sometimes.
            // A raw MODIFY COLUMN is often safest for existing ENUMs.
            DB::statement("ALTER TABLE posts MODIFY COLUMN status ENUM('draft', 'review', 'approved', 'published') NOT NULL DEFAULT 'draft'");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            // Revert back to original statuses. WARNING: Data loss for 'review'/'approved' statuses might occur or fail if they exist.
            // Ideally we map them back to draft.
            DB::statement("UPDATE posts SET status = 'draft' WHERE status IN ('review', 'approved')");
            DB::statement("ALTER TABLE posts MODIFY COLUMN status ENUM('draft', 'published') NOT NULL DEFAULT 'draft'");
        });
    }
};
