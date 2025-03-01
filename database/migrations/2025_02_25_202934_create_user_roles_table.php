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
        Schema::create('user_roles', function (Blueprint $table) {
            $table->id('UserRoleID');
            $table->unsignedBigInteger('EmployeeID');
            $table->unsignedBigInteger('RoleID');
            $table->timestamps();

            $table->foreign('EmployeeID')->references('EmployeeID')->on('employees')->onDelete('cascade');
            $table->foreign('RoleID')->references('RoleID')->on('roles')->onDelete('cascade');
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
