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
        Schema::create('employees', function (Blueprint $table) {
            $table->id('EmployeeID');
            $table->string('FirstName', 50);
            $table->string('LastName', 50);
            $table->string('Email', 100)->unique();
            $table->string('Phone', 15)->nullable();
            $table->date('HireDate');
            $table->string('JobTitle', 100);
            $table->unsignedBigInteger('DepartmentID')->nullable();
            $table->unsignedBigInteger('SupervisorID')->nullable();
            $table->decimal('Salary', 10, 2);
            $table->unsignedBigInteger('ContractID')->nullable();
            $table->enum('Status', ['Active', 'Inactive', 'On Leave'])->default('Active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
