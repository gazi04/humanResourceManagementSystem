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
        Schema::create('role_permissions', function (Blueprint $table) {
            $table->id('rolePermissionID');
            $table->unsignedBigInteger('roleID')->nullable();
            $table->unsignedBigInteger('permissionID')->nullable();
            $table->timestamps();

            $table->foreign('roleID')->references('roleID')->on('roles');
            $table->foreign('permissionID')->references('permissionID')->on('permissions');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('role_permissions');
    }
};
