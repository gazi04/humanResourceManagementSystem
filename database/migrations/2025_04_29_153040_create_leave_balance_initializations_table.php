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
        Schema::create('leave_balance_initializations', function (Blueprint $table) {
            $table->id('leaveBalanceInitID');
            $table->year('year')->unique();
            $table->boolean('isInitialized')->default(false);
            $table->timestamp('initializedAt')->nullable();
            $table->unsignedBigInteger('initializedBy')->nullable();
            $table->timestamps();

            $table->foreign('initializedBy')->references('employeeID')->on('employees');
        });
    }
};
