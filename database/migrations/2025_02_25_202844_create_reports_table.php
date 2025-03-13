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
        Schema::create('reports', function (Blueprint $table) {
            $table->id('reportID');
            $table->enum('reportType', ['Employee Count', 'Department Budget', 'Total Salaries', 'Leave Summary']);
            $table->unsignedBigInteger('generatedBy');
            $table->timestamp('generatedOn')->useCurrent();
            $table->json('reportData');
            $table->timestamps();

            $table->foreign('generatedBy')->references('employeeID')->on('employees');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
