<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('buyer_offers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('buyer_request_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->decimal('quantity', 12, 2)->default(0);
            $table->decimal('price', 12, 2)->default(0);
            $table->date('availability_date')->nullable();
            $table->text('message')->nullable();
            $table->string('status')->default('submitted');
            $table->foreignId('listing_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('buyer_offers');
    }
};
