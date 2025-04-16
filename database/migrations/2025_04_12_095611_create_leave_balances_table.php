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
        Schema::create('leave_balances', function (Blueprint $table) {
            $table->id('leaveBalanceID');
            $table->unsignedBigInteger('employeeID');
            $table->unsignedBigInteger('leaveTypeID');
            $table->decimal('remainingDays', 5, 2)->default(0);
            $table->decimal('usedDays', 5, 2)->default(0);
            $table->decimal('carriedOverDays', 5, 2)->default(0);
            $table->year('year');
            $table->foreign('employeeID')->references('employeeID')->on('employees');
            $table->foreign('leaveTypeID')->references('leaveTypeID')->on('leave_types');
            $table->timestamps();
        });
    }
};
