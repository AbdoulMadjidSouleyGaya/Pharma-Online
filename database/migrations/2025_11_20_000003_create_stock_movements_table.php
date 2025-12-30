<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pharmacy_id')->index();
            $table->unsignedBigInteger('pharma_product_id')->index();
            $table->unsignedBigInteger('user_id')->nullable()->index(); // pharmacien
            $table->enum('type', ['IN', 'OUT', 'ADJUSTMENT']);
            $table->integer('quantity'); // + pour IN, - pour OUT
            $table->integer('previous_quantity');
            $table->integer('new_quantity');
            $table->string('source')->nullable();    // 'order', 'manual', 'inventory', ...
            $table->string('reference')->nullable(); // ex: numéro commande
            $table->text('comment')->nullable();
            $table->timestamps();

            $table->foreign('pharmacy_id')
                ->references('id')
                ->on('pharmacies')
                ->onDelete('cascade');

            $table->foreign('pharma_product_id')
                ->references('id')
                ->on('pharma_products')
                ->onDelete('cascade');

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};
