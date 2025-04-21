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
        Schema::create('leave_type_role', function (Blueprint $table) {
            $table->id('leavTypeRoleID');
            $table->unsignedBigInteger('leaveTypeID');
            $table->unsignedBigInteger('roleID');

            $table->foreign('leaveTypeID')->references('leaveTypeID')->on('leave_types');
            $table->foreign('roleID')->references('roleID')->on('roles');
            $table->timestamps();
        });
    }
};
