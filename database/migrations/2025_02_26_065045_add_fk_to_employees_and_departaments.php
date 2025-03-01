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
        Schema::table('employees', function (Blueprint $table) {
            $table->foreign('DepartmentID')->references('DepartmentID')->on('departments');
        });

        Schema::table('employees', function (Blueprint $table) {
            $table->foreign('SupervisorID')->references('EmployeeID')->on('employees');
        });

        Schema::table('departments', function (Blueprint $table) {
            $table->foreign('SupervisorID')->references('EmployeeID')->on('employees');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropForeign(['DepartmentID']);
            $table->dropForeign(['SupervisorID']);
        });

        Schema::table('departments', function (Blueprint $table) {
            $table->dropForeign(['SupervisorID']);
        });
    }
};
