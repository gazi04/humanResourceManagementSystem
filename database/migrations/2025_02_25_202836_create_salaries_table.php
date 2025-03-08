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
        Schema::create('salaries', function (Blueprint $table) {
            $table->id('salaryID');
            $table->unsignedBigInteger('employeeID');
            $table->date('paymentDate');
            $table->decimal('amount', 10, 2);
            $table->timestamps();

            $table->foreign('employeeID')->references('employeeID')->on('employees');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('salaries');
    }
};
