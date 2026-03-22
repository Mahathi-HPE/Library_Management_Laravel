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
        Schema::create('BookAuthor', function (Blueprint $table) {
            $table->unsignedInteger('Bid');
            $table->unsignedInteger('Aid');

            $table->primary(['Bid', 'Aid']);
            $table->foreign('Bid')->references('Bid')->on('Books')->onDelete('cascade');
            $table->foreign('Aid')->references('Aid')->on('Author')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('BookAuthor');
    }
};
