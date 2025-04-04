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
        Schema::create('tickets', function (Blueprint $table) {
            $table->id('ticketID');
            $table->string('subject')->nullable();
            $table->text('description');
            $table->enum('status', ['open', 'closed'])->default('closed');
            $table->unsignedBigInteger('employeeID');
            $table->timestamps();

            $table->foreign('employeeID')->references('employeeID')->on('employees');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
