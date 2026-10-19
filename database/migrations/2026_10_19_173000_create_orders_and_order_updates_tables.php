<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('buyer_offer_id')->unique()->constrained()->cascadeOnDelete();
            $table->foreignId('buyer_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('seller_id')->constrained('users')->cascadeOnDelete();
            $table->decimal('quantity', 12, 2);
            $table->string('unit')->nullable();
            $table->decimal('total_price', 12, 2);
            $table->string('currency', 10)->default('ZMW');
            $table->string('fulfillment_method', 20);
            $table->string('fulfillment_location')->nullable();
            $table->text('fulfillment_notes')->nullable();
            $table->string('status', 30)->default('confirmed');
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->index(['buyer_id', 'status', 'created_at']);
            $table->index(['seller_id', 'status', 'created_at']);
        });

        Schema::create('order_updates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('status', 30);
            $table->text('note')->nullable();
            $table->timestamps();

            $table->index(['order_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_updates');
        Schema::dropIfExists('orders');
    }
};
