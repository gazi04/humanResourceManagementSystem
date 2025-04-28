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
        Schema::create('leave_requests', function (Blueprint $table) {
            $table->id('leaveRequestID');
            $table->unsignedBigInteger('employeeID');
            $table->unsignedBigInteger('leaveTypeID');
            $table->date('startDate');
            $table->date('endDate');
            $table->enum('durationType', ['fullDay', 'halfDay', 'multiDay']);
            $table->enum('halfDayType', ['firstHalf', 'secondHalf'])->nullable();
            $table->decimal('requestedDays', 5, 2);
            $table->text('reason');
            $table->enum('status', ['pending', 'approved', 'rejected', 'canceled'])->default('pending');
            $table->unsignedBigInteger('approvedBy')->nullable();
            $table->timestamp('approvedAt')->nullable();
            $table->text('rejectionReason')->nullable();
            $table->json('attachments')->nullable();

            $table->foreign('employeeID')->references('employeeID')->on('employees');
            $table->foreign('leaveTypeID')->references('leaveTypeID')->on('leave_types');
            $table->foreign('approvedBy')->references('employeeID')->on('employees');
            $table->timestamps();
        });
    }
};
