<?php

namespace App\Services\Interfaces;

use App\Models\Department;

interface DepartmentServiceInterface
{
    /**
     * @param array<string, mixed> $data
     */
    public function createDepartment(array $data): Department;

    /**
     * @param array<string, mixed> $data
     */
    public function updateDepartment(Department $department, array $data): Department;

    public function deleteDepartment(Department $department): void;
}
