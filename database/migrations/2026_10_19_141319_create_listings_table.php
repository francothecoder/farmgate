<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('listings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description');
            $table->decimal('quantity', 12, 2)->default(0);
            $table->string('unit')->nullable();
            $table->decimal('price', 12, 2)->default(0);
            $table->string('currency', 10)->default('ZMW');
            $table->boolean('negotiable')->default(false);
            $table->foreignId('country_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('province_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('district_id')->nullable()->constrained()->nullOnDelete();
            $table->string('location_detail')->nullable();
            $table->string('condition')->nullable();
            $table->string('availability')->default('available');
            $table->string('status')->default('pending_review');
            $table->string('contact_preference')->nullable();
            $table->string('featured_image')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('listings');
    }
};
