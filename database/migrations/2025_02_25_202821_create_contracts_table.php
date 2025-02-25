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
        Schema::create('contracts', function (Blueprint $table) {
            $table->id('ContractID');
            $table->unsignedBigInteger('EmployeeID');
            $table->date('StartDate');
            $table->date('EndDate')->nullable();
            $table->enum('ContractType', ['Full-Time', 'Part-Time', 'Temporary', 'Internship']);
            $table->decimal('Salary', 10, 2);
            $table->timestamps();

            // Foreign key
            $table->foreign('EmployeeID')->references('EmployeeID')->on('employees')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contracts');
    }
};
