<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add indexes used by the catalog, order lists and seller order queries.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->index(['is_active', 'sold_count']);
            $table->index(['is_active', 'rating']);
            $table->index(['is_active', 'created_at']);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->index(['status', 'created_at']);
            $table->index('created_at');
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->index(['order_id', 'product_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropIndex(['order_id', 'product_id']);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex(['status', 'created_at']);
            $table->dropIndex(['created_at']);
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex(['is_active', 'sold_count']);
            $table->dropIndex(['is_active', 'rating']);
            $table->dropIndex(['is_active', 'created_at']);
        });
    }
};
