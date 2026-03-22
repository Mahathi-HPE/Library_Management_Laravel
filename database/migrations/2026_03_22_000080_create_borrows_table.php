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
        Schema::create('Borrows', function (Blueprint $table) {
            $table->increments('BorrowId');
            $table->unsignedInteger('Cid');
            $table->unsignedInteger('Mid');
            $table->unsignedInteger('Bid')->nullable();
            $table->date('Bdate')->nullable();
            $table->enum('BorrowStatus', ['Pending', 'Approved', 'Rejected'])->nullable();
            $table->float('Fine')->default(0);
            $table->enum('FineStatus', ['Paid', 'Not Paid', 'NA'])->default('NA');
            $table->enum('ReturnStatus', ['Not Returned', 'Pending', 'Approved'])->default('Not Returned');

            $table->foreign('Cid')->references('Cid')->on('Copies')->onDelete('cascade');
            $table->foreign('Mid')->references('Mid')->on('Members')->onDelete('cascade');
            $table->foreign('Bid')->references('Bid')->on('Books')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('Borrows');
    }
};
