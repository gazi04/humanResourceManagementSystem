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
            $table->id('SalaryID');
            $table->unsignedBigInteger('EmployeeID');
            $table->date('PaymentDate');
            $table->decimal('Amount', 10, 2);
            $table->timestamps();

            $table->foreign('EmployeeID')->references('EmployeeID')->on('employees')->onDelete('cascade');
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
