<?php

namespace App\Services\Interfaces;

use App\Models\Department;
use Illuminate\Pagination\LengthAwarePaginator;

interface DepartmentServiceInterface
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function createDepartment(array $data): Department;

    /**
     * @param  array<string, mixed>  $data
     */
    public function updateDepartment(Department $department, array $data): Department;

    public function deleteDepartment(Department $department): void;

    public function showDepartments(): LengthAwarePaginator;

    public function updateManager(Department $department, int $managerID): void;
}
