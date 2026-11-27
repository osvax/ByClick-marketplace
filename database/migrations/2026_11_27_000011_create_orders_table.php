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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('number', 32)->unique();
            $table->string('status', 20)->default('new')->index();
            $table->string('customer_name');
            $table->string('customer_phone', 32);
            $table->string('customer_email');
            $table->string('delivery_method', 20);
            $table->foreignId('delivery_point_id')->nullable()->constrained()->nullOnDelete();
            $table->string('address')->nullable();
            $table->string('payment_method', 20);
            $table->text('comment')->nullable();
            $table->unsignedInteger('subtotal');
            $table->unsignedInteger('discount')->default(0);
            $table->unsignedInteger('delivery_price')->default(0);
            $table->unsignedInteger('total');
            $table->foreignId('promo_code_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamp('placed_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
