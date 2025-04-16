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
        Schema::create('leave_calendar_events', function (Blueprint $table) {
            $table->id('leaveCalendarEventID');
            $table->morphs('leaveEvent'); // Can be leave_request or holiday
            $table->string('title');
            $table->date('startDate');
            $table->date('endDate');
            $table->enum('type', ['leave', 'holiday']); // leave, holiday
            $table->json('meta')->nullable();
            $table->timestamps();
        });
    }
};
