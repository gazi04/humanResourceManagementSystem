<?php

namespace Tests\Feature;

use App\Models\Department;
use App\Models\Employee;
use App\Models\EmployeeRole;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class DepartmentControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Set up the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $employee = Employee::create([
            'firstName' => 'gazi',
            'lastName' => 'halili',
            'email' => 'gaz@gmail.com',
            'phone' => '045618376',
            'password' => Hash::make('gazi04'),
        ]);

        $role = Role::create(['roleName' => 'admin']);

        EmployeeRole::create([
            'employeeID' => $employee['employeeID'],
            'roleID' => $role['roleID'],
        ]);

        Auth::guard('employee')->login($employee);
    }

    public function test_create_department_validation_rules(): void
    {
        // Simulate a POST request with invalid data
        $response = $this->post(route('create-department'), [
            // Missing required fields
        ]);

        // Assert that the user is redirected back with validation errors
        $response->assertRedirect('/');

        // Assert that the specific validation error messages are present in the session
        $response->assertSessionHasErrors([
            'departmentName' => 'Emri i departamentit është i detyrueshëm.',
            'supervisorID' => 'ID e mbikëqyrësit është e detyrueshme.',
        ]);

        // Simulate a POST request with invalid departmentName (not a string)
        $response = $this->post(route('create-department'), [
            'departmentName' => 123, // Invalid type
            'supervisorID' => 1,
        ]);

        // Assert that the user is redirected back with validation errors
        $response->assertRedirect('/');

        // Assert that the specific validation error message is present in the session
        $response->assertSessionHasErrors([
            'departmentName' => 'Emri i departamentit duhet të jetë një varg tekstual.',
        ]);

        // Simulate a POST request with a duplicate departmentName
        $department = Department::create([
            'departmentID' => '100',
            'departmentName' => 'testDepartment',
        ]);

        $response = $this->post(route('create-department'), [
            'departmentName' => $department->departmentName, // Duplicate name
            'supervisorID' => 1,
        ]);

        // Assert that the user is redirected back with validation errors
        $response->assertRedirect('/');

        // Assert that the specific validation error message is present in the session
        $response->assertSessionHasErrors([
            'departmentName' => 'Ekziston tashmë një departament me këtë emër.',
        ]);

        // Simulate a POST request with an invalid supervisorID (non-existent)
        $response = $this->post(route('create-department'), [
            'departmentName' => 'New Department',
            'supervisorID' => 999, // Non-existent supervisorID
        ]);

        // Assert that the user is redirected back with validation errors
        $response->assertRedirect('/');

        // Assert that the specific validation error message is present in the session
        $response->assertSessionHasErrors([
            'supervisorID' => 'ID e mbikëqyrësit nuk egziston ne tabelen e punonjesve.',
        ]);
    }

    public function test_delete_department_validation_rules(): void
    {
        // Simulate a DELETE request with missing departmentID
        $response = $this->delete(route('delete-department'), [
            // Missing departmentID
        ]);

        // Assert that the user is redirected back with validation errors
        $response->assertRedirect('/');

        // Assert that the specific validation error message is present in the session
        $response->assertSessionHasErrors([
            'departmentID' => 'ID e departamentit është e detyrueshme.',
        ]);

        // Simulate a DELETE request with an invalid departmentID type
        $response = $this->delete(route('delete-department'), [
            'departmentID' => 'invalid', // Invalid type
        ]);

        // Assert that the user is redirected back with validation errors
        $response->assertRedirect('/');

        // Assert that the specific validation error message is present in the session
        $response->assertSessionHasErrors([
            'departmentID' => 'ID e departamentit duhet të jetë një numër i plotë.',
        ]);

        // Simulate a DELETE request with a departmentID less than 1
        $response = $this->delete(route('delete-department'), [
            'departmentID' => 0, // Invalid value
        ]);

        // Assert that the user is redirected back with validation errors
        $response->assertRedirect('/');

        // Assert that the specific validation error message is present in the session
        $response->assertSessionHasErrors([
            'departmentID' => 'ID e departamentit duhet të jetë më e madhe se 0.',
        ]);

        // Simulate a DELETE request with a non-existent departmentID
        $response = $this->delete(route('delete-department'), [
            'departmentID' => 999, // Non-existent departmentID
        ]);

        // Assert that the user is redirected back with validation errors
        $response->assertRedirect('/');

        // Assert that the specific validation error message is present in the session
        $response->assertSessionHasErrors([
            'departmentID' => 'Departamenti me këtë ID nuk egziston.',
        ]);
    }

    public function test_update_department_validation_rules(): void
    {
        // Simulate a PATCH request with missing fields
        $response = $this->patch(route('update-department'), [
            // Missing departmentID and newDepartmentName
        ]);

        // Assert that the user is redirected back with validation errors
        $response->assertRedirect('/');

        // Assert that the specific validation error messages are present in the session
        $response->assertSessionHasErrors([
            'departmentID' => 'ID e departamentit është e detyrueshme.',
            'newDepartmentName' => 'Emri i ri i departamentit është i detyrueshëm.',
        ]);

        // Simulate a PATCH request with an invalid departmentID type
        $response = $this->patch(route('update-department'), [
            'departmentID' => 'invalid', // Invalid type
            'newDepartmentName' => 'Updated Department Name',
        ]);

        // Assert that the user is redirected back with validation errors
        $response->assertRedirect('/');

        // Assert that the specific validation error message is present in the session
        $response->assertSessionHasErrors([
            'departmentID' => 'ID e departamentit duhet të jetë një numër i plotë.',
        ]);

        // Simulate a PATCH request with a departmentID less than 1
        $response = $this->patch(route('update-department'), [
            'departmentID' => 0, // Invalid value
            'newDepartmentName' => 'Updated Department Name',
        ]);

        // Assert that the user is redirected back with validation errors
        $response->assertRedirect('/');

        // Assert that the specific validation error message is present in the session
        $response->assertSessionHasErrors([
            'departmentID' => 'ID e departamentit duhet të jetë më e madhe se 0.',
        ]);

        // Simulate a PATCH request with a non-existent departmentID
        $response = $this->patch(route('update-department'), [
            'departmentID' => 999, // Non-existent departmentID
            'newDepartmentName' => 'Updated Department Name',
        ]);

        // Assert that the user is redirected back with validation errors
        $response->assertRedirect('/');

        // Assert that the specific validation error message is present in the session
        $response->assertSessionHasErrors([
            'departmentID' => 'Departamenti me këtë ID nuk egziston.',
        ]);

        // Simulate a PATCH request with a duplicate newDepartmentName
        $department = Department::create([
            'departmentID' => '100',
            'departmentName' => 'testDepartment',
        ]);

        $response = $this->patch(route('update-department'), [
            'departmentID' => $department->departmentID,
            'newDepartmentName' => $department->departmentName, // Duplicate name
        ]);

        // Assert that the user is redirected back with validation errors
        $response->assertRedirect('/');

        // Assert that the specific validation error message is present in the session
        $response->assertSessionHasErrors([
            'newDepartmentName' => 'Ekziston tashmë një departament me këtë emër.',
        ]);
    }

    /**
     * Test creating a department with valid data.
     */
    public function test_create_department_with_valid_data(): void
    {
        $supervisor = Employee::create([
            'firstName' => 'gazi',
            'lastName' => 'halili',
            'email' => 'gazi@gmail.com',
            'phone' => '045618476',
            'password' => Hash::make('gazi04'),
        ]);

        // Simulate a POST request with valid data
        $response = $this->post(route('create-department'), [
            'departmentName' => 'IT Department',
            'supervisorID' => $supervisor->employeeID,
        ]);

        // Assert that the department was created in the database
        $this->assertDatabaseHas('departments', [
            'departmentName' => 'IT Department',
            'supervisorID' => $supervisor->employeeID,
        ]);

        // Assert that the user is redirected to the admin dashboard
        $response->assertRedirect(route('admin-dashboard'));

        // Assert that the success message is present in the session
        $response->assertSessionHas('message', 'Departamenti është krijuar me sukses.');
    }

    /**
     * Test creating a department with invalid data.
     */
    public function test_create_department_with_invalid_data(): void
    {
        // Simulate a POST request with invalid data (missing required fields)
        $response = $this->post(route('create-department'), [
            'departmentName' => '', // Empty department name
            'supervisorID' => null, // Missing supervisor ID
        ]);

        // Assert that the response contains validation errors
        $response->assertSessionHasErrors(['departmentName', 'supervisorID']);

        // Assert that no department was created in the database
        $this->assertDatabaseCount('departments', 0);
    }

    /**
     * Test deleting an existing department.
     */
    public function test_delete_existing_department(): void
    {
        // Create a department to delete
        $department = Department::create([
            'departmentID' => '100',
            'departmentName' => 'testDepartment',
        ]);

        // Simulate a DELETE request to delete the department
        $response = $this->delete(route('delete-department'), [
            'departmentID' => $department->departmentID,
        ]);

        // Assert that the department was deleted from the database
        $this->assertDatabaseMissing('departments', [
            'departmentID' => $department->departmentID,
        ]);

        // Assert that the user is redirected to the admin dashboard
        $response->assertRedirect(route('admin-dashboard'));

        // Assert that the success message is present in the session
        $response->assertSessionHas('message', 'Departamenti është fshirë me sukses.');
    }

    /**
     * Test deleting a non-existent department.
     */
    public function test_delete_non_existent_department(): void
    {
        // Simulate a DELETE request with an invalid department ID
        $response = $this->delete(route('delete-department'), [
            'departmentID' => 999, // Non-existent department ID
        ]);

        // Assert that no department was deleted from the database
        $this->assertDatabaseCount('departments', 0);

        // Assert that the user is redirected back with validation errors
        $response->assertRedirect('/'); // Redirects back to the previous URL

        // Assert that the validation error message is present in the session
        $response->assertSessionHasErrors([
            'departmentID' => 'Departamenti me këtë ID nuk egziston.',
        ]);
    }

    /**
     * Test updating an existing department.
     */
    public function test_update_existing_department(): void
    {
        // Create a department to update
        $department = Department::create([
            'departmentID' => '100',
            'departmentName' => 'testDepartment',
        ]);

        // Simulate a PATCH request to update the department
        $response = $this->patch(route('update-department'), [
            'departmentID' => $department->departmentID,
            'newDepartmentName' => 'Updated Department Name',
        ]);

        // Assert that the department was updated in the database
        $this->assertDatabaseHas('departments', [
            'departmentID' => $department->departmentID,
            'departmentName' => 'Updated Department Name',
        ]);

        // Assert that the user is redirected to the admin dashboard
        $response->assertRedirect(route('admin-dashboard'));
    }

    /**
     * Test updating a non-existent department.
     */
    public function test_update_non_existent_department(): void
    {
        // Simulate a PATCH request with an invalid department ID
        $response = $this->patch(route('update-department'), [
            'departmentID' => 999, // Non-existent department ID
            'newDepartmentName' => 'Updated Department Name',
        ]);

        // Assert that no department was updated in the database
        $this->assertDatabaseCount('departments', 0);

        // Assert that the user is redirected back with validation errors
        $response->assertRedirect('/'); // Redirects back to the previous URL

        // Assert that the validation error message is present in the session
        $response->assertSessionHasErrors([
            'departmentID' => 'Departamenti me këtë ID nuk egziston.',
        ]);
    }
}
