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
        Schema::create('vacations', function (Blueprint $table) {
            $table->id('VacationID');
            $table->unsignedBigInteger('EmployeeID');
            $table->enum('LeaveType', ['Vacation', 'Sick Leave', 'Maternity/Paternity', 'Unpaid']);
            $table->date('StartDate');
            $table->date('EndDate');
            $table->enum('Status', ['Pending', 'Approved', 'Rejected'])->default('Pending');
            $table->timestamps();

            $table->foreign('EmployeeID')->references('EmployeeID')->on('employees')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vacations');
    }
};
