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
        Schema::create('leave_approvals', function (Blueprint $table) {
            $table->id('leaveApprovalID');
            $table->unsignedBigInteger('leave_request_id');
            $table->unsignedBigInteger('approver_id');
            $table->enum('status', ['pending', 'approved', 'rejected', 'canceled'])->default('pending');
            $table->text('comments')->nullable();
            $table->timestamps();
        });
    }
};
