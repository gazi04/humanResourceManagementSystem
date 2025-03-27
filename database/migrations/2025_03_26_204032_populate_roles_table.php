<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('roles')->insert([
            [
                'roleName' => 'admin',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'roleName' => 'hr',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'roleName' => 'employee',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'roleName' => 'manager',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
