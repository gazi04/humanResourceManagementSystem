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
        Schema::create('employees', function (Blueprint $table) {
            $table->id('employeeID');
            $table->string('firstName', 50);
            $table->string('lastName', 50);
            $table->string('email', 100)->unique();
            $table->string('password');
            $table->string('phone', 15);
            $table->date('hireDate')->nullable();
            $table->string('jobTitle', 100)->nullable();
            $table->unsignedBigInteger('departmentID')->nullable();
            $table->unsignedBigInteger('supervisorID')->nullable();
            $table->decimal('salary', 10, 2)->nullable();
            $table->unsignedBigInteger('contractID')->nullable();
            $table->enum('status', ['Active', 'Inactive', 'On Leave'])->default('Inactive');
            $table->string('remember_token')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
