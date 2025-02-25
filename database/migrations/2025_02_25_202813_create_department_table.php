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
        Schema::create('department', function (Blueprint $table) {
            $table->id('DepartmentID');
            $table->string('DepartmentName', 100);
            $table->unsignedBigInteger('SupervisorID')->nullable();
            $table->decimal('Budget', 15, 2)->nullable();
            $table->timestamps();

            // Foreign key
            $table->foreign('SupervisorID')->references('EmployeeID')->on('employees')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('department');
    }
};
