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
        Schema::create('holidays', function (Blueprint $table) {
            $table->id('holidayID');
            $table->string('name');
            $table->date('date');
            $table->boolean('repeatsAnnually')->default(true);
            $table->text('description')->nullable();
            $table->boolean('isActive')->default(true);
            $table->timestamps();
        });
    }
};
