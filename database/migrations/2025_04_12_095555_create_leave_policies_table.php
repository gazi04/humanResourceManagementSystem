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
        Schema::create('leave_policies', function (Blueprint $table) {
            $table->id('leavePolicyID');
            $table->unsignedBigInteger('leaveTypeID');
            $table->integer('annualQuota')->default(0);
            $table->integer('maxConsecutiveDays')->nullable();
            $table->boolean('allowHalfDay')->default(false);
            $table->integer('probationPeriodDays')->default(0); // Specifies the minimum number of days an employee must work before being eligible for leave
            $table->decimal('carryOverLimit', 5, 2)->default(0);
            $table->json('restricedDays')->nullable();
            $table->json('requirenments')->nullable();

            $table->foreign('leaveTypeID')->references('leaveTypeID')->on('leave_types');
            $table->timestamps();
        });
    }
};
