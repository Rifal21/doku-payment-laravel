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
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // Kode kupon unik
            $table->unsignedTinyInteger('discount_percentage');
            $table->boolean('is_active')->default(true);
            $table->timestamp('expires_at')->nullable();
            $table->integer('max_uses')->nullable();
            $table->integer('used_count')->default(0);
            $table->string('status')->default('tersedia');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};
