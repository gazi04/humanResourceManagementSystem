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
        Schema::create('employee_roles', function (Blueprint $table) {
            $table->id('employeeRoleID');
            $table->unsignedBigInteger('employeeID');
            $table->unsignedBigInteger('roleID');
            $table->timestamps();

            $table->foreign('employeeID')->references('employeeID')->on('employees');
            $table->foreign('roleID')->references('roleID')->on('roles');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_roles');
    }
};
