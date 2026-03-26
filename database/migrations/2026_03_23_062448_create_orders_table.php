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
            $table->timestamps();
            $table->foreignId('user_id')->constrained()->onDelete('restrict');
            $table->string('address');
            $table->enum('status', ['preparing', 'delivering', 'delivered', 'cancelled'])->default('preparing');
            $table->dateTime('delivery_time');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
