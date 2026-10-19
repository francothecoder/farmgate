<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->timestamp('seller_verified_at')->nullable();
            $table->foreignId('seller_verified_by')->nullable()->constrained('users')->nullOnDelete();
        });

        Schema::create('seller_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('buyer_offer_id')->unique()->constrained()->cascadeOnDelete();
            $table->foreignId('seller_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('reviewer_id')->constrained('users')->cascadeOnDelete();
            $table->unsignedTinyInteger('rating');
            $table->text('body')->nullable();
            $table->timestamps();

            $table->index(['seller_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('seller_reviews');

        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('seller_verified_by');
            $table->dropColumn('seller_verified_at');
        });
    }
};
