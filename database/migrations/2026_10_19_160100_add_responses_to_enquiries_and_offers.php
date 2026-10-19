<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('enquiries', function (Blueprint $table) {
            $table->text('response_message')->nullable();
            $table->timestamp('responded_at')->nullable();
        });

        Schema::table('buyer_offers', function (Blueprint $table) {
            $table->text('response_message')->nullable();
            $table->timestamp('responded_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('buyer_offers', function (Blueprint $table) {
            $table->dropColumn(['response_message', 'responded_at']);
        });

        Schema::table('enquiries', function (Blueprint $table) {
            $table->dropColumn(['response_message', 'responded_at']);
        });
    }
};
