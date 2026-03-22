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
        Schema::create('Copies', function (Blueprint $table) {
            $table->increments('Cid');
            $table->unsignedInteger('Bid');
            $table->enum('Status', ['Available', 'Rented'])->default('Available');

            $table->foreign('Bid')->references('Bid')->on('Books')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('Copies');
    }
};
