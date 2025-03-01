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
            $table->id('ReportID');
            $table->enum('ReportType', ['Employee Count', 'Department Budget', 'Total Salaries', 'Leave Summary']);
            $table->unsignedBigInteger('GeneratedBy');
            $table->timestamp('GeneratedOn')->useCurrent();
            $table->json('ReportData');
            $table->timestamps();

            $table->foreign('GeneratedBy')->references('EmployeeID')->on('employees')->onDelete('cascade');
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
