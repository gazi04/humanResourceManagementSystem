@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
@endif
@if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="container py-4">
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h2 class="mb-0">Employee Profile</h2>
        </div>

        <div class="card-body">
            <div class="row">
                <!-- Left Column - Basic Info -->
                <div class="col-md-6">
                    <div class="mb-4">
                        <h4 class="text-primary border-bottom pb-2">Personal Information</h4>
                        <dl class="row">
                            <dt class="col-sm-4">Employee ID:</dt>
                            <dd class="col-sm-8">{{ $employee->employeeID }}</dd>

                            <dt class="col-sm-4">Full Name:</dt>
                            <dd class="col-sm-8">{{ $employee->firstName }} {{ $employee->lastName }}</dd>

                            <dt class="col-sm-4">Email:</dt>
                            <dd class="col-sm-8">{{ $employee->email }}</dd>

                            <dt class="col-sm-4">Phone:</dt>
                            <dd class="col-sm-8">{{ $employee->phone }}</dd>
                        </dl>
                    </div>
                </div>

                <!-- Right Column - Employment Info -->
                <div class="col-md-6">
                    <div class="mb-4">
                        <h4 class="text-primary border-bottom pb-2">Employment Details</h4>
                        <dl class="row">
                            <dt class="col-sm-4">Status:</dt>
                            <dd class="col-sm-8">
                                <span class="badge badge-{{ $employee->status === 'Active' ? 'success' : 'secondary' }}">
                                    {{ $employee->status }}
                                </span>
                            </dd>

                            <dt class="col-sm-4">Role:</dt>
                            <dd class="col-sm-8">{{ $employee->roleName ?? 'N/A' }}</dd>

                            <dt class="col-sm-4">Department:</dt>
                            <dd class="col-sm-8">{{ $employee->departmentName ?? 'N/A' }}</dd>

                            <dt class="col-sm-4">Supervisor:</dt>
                            <dd class="col-sm-8">
                                @if($employee->supervisorFirstName)
                                    {{ $employee->supervisorFirstName }} {{ $employee->supervisorLastName }}
                                @else
                                    N/A
                                @endif
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>

            <!-- Additional Information Section -->
            <div class="mt-4">
                <h4 class="text-primary border-bottom pb-2">Additional Information</h4>
                <dl class="row">
                    <dt class="col-sm-2">Hire Date:</dt>
                    <dd class="col-sm-10">{{ $employee->hireDate ?? 'Not specified' }}</dd>

                    <dt class="col-sm-2">Job Title:</dt>
                    <dd class="col-sm-10">{{ $employee->jobTitle ?? 'Not specified' }}</dd>
                </dl>
            </div>
        </div>

        <h2>Add a new contract</h2>
        <form method="POST" action="{{ route('hr.employee.contract.upload') }}" enctype="multipart/form-data">
            @csrf
            <input type='hidden' name='employeeID' value='{{ $employee->employeeID }}' />
            <input type='file' name='contract_file' />
            <input type='submit' value='Upload Contract'/>
        </form>

        <div class="card-footer text-right">
            <a href="" class="btn btn-primary">
                <i class="fas fa-edit"></i> Edit Profile
            </a>
            <a href="" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to List
            </a>
        </div>
    </div>
</div>

<div class="list-group mb-4">
    <h1>Contrac List</h1>
    @forelse($contracts as $contract)
        <div class="list-group-item">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-1">{{ pathinfo($contract->contractPath, PATHINFO_FILENAME) }}</h5>
                    <small class="text-muted">Uploaded: {{ \Carbon\Carbon::parse($contract->created_at)->format('M d, Y H:i') }}</small>
                </div>
                <div class="btn-group">
                    <form method="POST" action="{{ route('hr.employee.contract.download') }}" enctype="multipart/form-data">
                        @csrf
                        <input type='hidden' name='contractID' value="{{ $contract->contractID }}" />
                        <input type='submit' value='Dowload Contract'/>
                    </form>
                    <br>

                    <form action="{{ route('hr.employee.contract.update') }}" method="POST" class="mr-2" enctype="multipart/form-data">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="contractID" value="{{ $contract->contractID }}">
                        <input type="hidden" name="employeeID" value="{{ $employee->employeeID }}">
                        <input type='file' name='contract_file' />
                        <button type="submit" class="btn btn-sm btn-outline-secondary">Update</button>
                    </form>

                    <form action="{{ route('hr.employee.contract.delete') }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <input type="hidden" name="contractID" value="{{ $contract->contractID }}">
                        <button type="submit" class="btn btn-sm btn-outline-danger"
                                              onclick="return confirm('Are you sure you want to delete this contract?')">
                            Delete
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @empty
        <div class="list-group-item">
            <p class="mb-0">No contracts found.</p>
        </div>
    @endforelse
</div>

{{-- Pagination --}}
<div class="d-flex justify-content-center">
    {{ $contracts->appends(['employeeID' => $employee->employeeID])->links() }}
</div>
