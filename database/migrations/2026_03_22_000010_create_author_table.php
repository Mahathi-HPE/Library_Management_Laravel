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
        Schema::create('Author', function (Blueprint $table) {
            $table->increments('Aid');
            $table->string('AuthLoc', 255)->nullable();
            $table->string('AuthEmail', 255)->unique()->nullable();
            $table->string('AuthName', 255)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('Author');
    }
};
