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
        Schema::create('UserMember', function (Blueprint $table) {
            $table->unsignedInteger('Uid');
            $table->unsignedInteger('Mid');

            $table->primary(['Uid', 'Mid']);
            $table->foreign('Uid')->references('Uid')->on('Users')->onDelete('cascade');
            $table->foreign('Mid')->references('Mid')->on('Members')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('UserMember');
    }
};
