<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Employee;
use App\Models\EmployeeRole;
use Hash;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            DepartmentSeeder::class,
            EmployeeSeeder::class,
            LeaveTypeSeeder::class,
            LeaveRequestSeeder::class,
            LeaveBalanceSeeder::class,
        ]);

        $admin = Employee::create([
            'firstName' => 'gazi',
            'lastName' => 'gazi',
            'email' => 'gazi@gmail.com',
            'password' => Hash::make('gazigazi'),
            'phone' => '045681376',
        ]);
        $hr = Employee::create([
            'firstName' => 'gazi',
            'lastName' => 'gazi',
            'email' => 'gaz123221@gmail.com',
            'password' => Hash::make('123123'),
            'phone' => '045681371',
        ]);
        $manager = Employee::create([
            'firstName' => 'gazi',
            'lastName' => 'gazi',
            'email' => 'gazi32@gmail.com',
            'password' => Hash::make('123123'),
            'phone' => '045681370',
        ]);

        $employeeRole = EmployeeRole::create([
            'employeeID' => $admin['employeeID'],
            'roleID' => 1,
        ]);

        EmployeeRole::create([
            'employeeID' => $manager->employeeID,
            'roleID' => 4,
        ]);
        EmployeeRole::create([
            'employeeID' => $hr->employeeID,
            'roleID' => 2,
        ]);

    }
}
