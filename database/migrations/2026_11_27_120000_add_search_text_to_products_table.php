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
     * A normalized lowercase search string keeps product search
     * case-insensitive on every database (SQLite LIKE is not Unicode-aware).
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('search_text', 512)->nullable()->after('brand');
        });

        // Backfill existing products without loading Eloquent models.
        DB::table('products')
            ->select(['id', 'name', 'brand'])
            ->orderBy('id')
            ->chunkById(200, function ($products): void {
                foreach ($products as $product) {
                    DB::table('products')->where('id', $product->id)->update([
                        'search_text' => mb_strtolower(trim($product->name.' '.($product->brand ?? ''))),
                    ]);
                }
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('search_text');
        });
    }
};
