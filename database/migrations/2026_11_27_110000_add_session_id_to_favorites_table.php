<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Favorites become available to guests: a favorite belongs either to a
     * user (user_id) or to a browser session (session_id).
     */
    public function up(): void
    {
        Schema::table('favorites', function (Blueprint $table) {
            $table->string('session_id', 191)->nullable()->after('user_id');

            // The composite unique index also served the user_id foreign key,
            // so give it a dedicated index before dropping the unique one.
            $table->index('user_id');
            $table->index('session_id');

            $table->dropUnique(['user_id', 'product_id']);
            $table->foreignId('user_id')->nullable()->change();
            $table->unique(['session_id', 'product_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove guest favorites before user_id becomes required again.
        DB::table('favorites')->whereNull('user_id')->delete();

        Schema::table('favorites', function (Blueprint $table) {
            $table->dropUnique(['session_id', 'product_id']);
            $table->dropIndex(['session_id']);

            $table->foreignId('user_id')->nullable(false)->change();
            $table->unique(['user_id', 'product_id']);
            $table->dropIndex(['user_id']);
        });
    }
};
