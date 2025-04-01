<div class="container">
    <h1>Employees</h1>
    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="table-responsive">
        <table class="table table-striped table-bordered">
            <thead class="thead-dark">
                <tr>
                    <th>ID</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Status</th>
                    <th>Role</th>
                    <th>Department</th>
                    <th>Supervisor</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($employees as $employee)
                    <tr>
                        <td>{{ $employee->employeeID }}</td>
                        <td>{{ $employee->firstName }}</td>
                        <td>{{ $employee->lastName }}</td>
                        <td>{{ $employee->email }}</td>
                        <td>{{ $employee->phone }}</td>
                        <td>
                            <span class="badge badge-{{ $employee->status === 'Active' ? 'success' : 'secondary' }}">
                                {{ $employee->status }}
                            </span>
                        </td>
                        <td>{{ $employee->roleName }}</td>
                        <td>{{ $employee->departmentName ?? 'N/A' }}</td>
                        <td>
                            @if($employee->supervisorFirstName)
                                {{ $employee->supervisorFirstName }} {{ $employee->supervisorLastName }}
                            @else
                                N/A
                            @endif
                        </td>
                        <td>
                            <form action="{{ route('hr.employee.profile') }}" method="GET">
                                <input type='hidden' name='employeeID' value='{{ $employee->employeeID }}'/>
                                <input type='submit' value='Show Profile' />
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="text-center">No employees found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination Links --}}
    <div class="d-flex justify-content-center">
        {{ $employees->links() }}
    </div>

    <div class="mt-3">
        <a href="" class="btn btn-success">Add New Employee</a>
    </div>
</div>
