<?php

use App\Models\Product;
use App\Models\User;
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
        Schema::create('carts', function (Blueprint $table) {
            $table->id();
            $table->integer('quantity');

            $table->foreignIdFor(Product::class, 'product_id')
                ->constrained()
                ->onDelete('cascade');

            $table->foreignIdFor(User::class, 'user_id')
                ->nullable()
                ->constrained()
                ->onDelete('cascade');

            $table->uuid('guest_token')
                ->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'product_id']);
            $table->unique(['guest_token', 'product_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('carts');
    }
};
