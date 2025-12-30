<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('stock_alerts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pharmacy_id')->index();
            $table->unsignedBigInteger('pharma_product_id')->index();
            $table->unsignedBigInteger('user_id')->index(); // pharmacien concerné

            // low = stock bas, out = rupture
            $table->enum('level', ['low', 'out']);
            $table->string('message');
            $table->boolean('is_read')->default(false);

            $table->timestamps();

            $table->foreign('pharmacy_id')
                ->references('id')->on('pharmacies')
                ->onDelete('cascade');

            $table->foreign('pharma_product_id')
                ->references('id')->on('pharma_products')
                ->onDelete('cascade');

            $table->foreign('user_id')
                ->references('id')->on('users')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_alerts');
    }
};
