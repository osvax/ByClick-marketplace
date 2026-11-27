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
        Schema::create('delivery_points', function (Blueprint $table) {
            $table->id();
            $table->string('city', 100)->index();
            $table->string('address');
            $table->string('schedule')->nullable();
            $table->string('type', 20)->default('pickup');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('delivery_points');
    }
};
