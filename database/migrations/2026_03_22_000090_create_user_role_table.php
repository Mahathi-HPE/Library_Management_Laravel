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
        Schema::create('UserRole', function (Blueprint $table) {
            $table->unsignedInteger('Rid');
            $table->unsignedInteger('Uid');

            $table->primary(['Rid', 'Uid']);
            $table->foreign('Rid')->references('Rid')->on('Roles')->onDelete('cascade');
            $table->foreign('Uid')->references('Uid')->on('Users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('UserRole');
    }
};
