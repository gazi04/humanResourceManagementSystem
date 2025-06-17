<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leave Types & Requests</title>
    <!-- Tailwind CSS CDN for quick setup -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">

    <!-- Navigation Bar -->
    <nav class="bg-gray-800 p-4 shadow-lg">
        <div class="container mx-auto flex justify-between items-center">
            <div class="text-white font-bold text-xl">
                Leave Management
            </div>
            <ul class="flex space-x-4">
                <li><a href="{{-- route('leaves.index') --}}#" class="text-gray-300 hover:text-white px-3 py-2 rounded-md text-sm font-medium transition duration-300 ease-in-out">Leave</a></li>
                <li><a href="{{-- route('employees.index') --}}#" class="text-gray-300 hover:text-white px-3 py-2 rounded-md text-sm font-medium transition duration-300 ease-in-out">Employee</a></li>
                <li><a href="{{-- route('reports.index') --}}#" class="text-gray-300 hover:text-white px-3 py-2 rounded-md text-sm font-medium transition duration-300 ease-in-out">Reports</a></li>
                <li><a href="{{-- route('settings.index') --}}#" class="text-gray-300 hover:text-white px-3 py-2 rounded-md text-sm font-medium transition duration-300 ease-in-out">Settings</a></li>
            </ul>
        </div>
    </nav>

    <div class="container mx-auto bg-white p-8 rounded-lg shadow-xl mt-8 mb-8">
        <h1 class="text-3xl font-extrabold text-gray-800 mb-6 border-b pb-4">Manage Leave Types</h1>

        <!-- Create New Leave Type Button -->
        <div class="flex justify-end mb-6">
            <a href="{{ route('hr.leave-type.create') }}"
               class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-lg shadow-md transition duration-300 ease-in-out transform hover:scale-105">
                Create New Leave Type
            </a>
        </div>

        @if ($leaveTypes->isEmpty())
            <p class="text-center text-gray-600 py-8 text-lg">No leave types found. Click "Create New Leave Type" to add one.</p>
        @else
            <!-- Leave Types Table -->
            <div class="overflow-x-auto rounded-lg border border-gray-200 shadow-sm">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="py-3 px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                            <th class="py-3 px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                            <th class="py-3 px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                            <th class="py-3 px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Is Paid</th>
                            <th class="py-3 px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Requires Approval</th>
                            <th class="py-3 px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Is Active</th>
                            <th class="py-3 px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Policy ID</th>
                            <!-- Add more headers if you select more fields -->
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($leaveTypes as $leaveType)
                            <tr class="hover:bg-gray-100 transition duration-150 ease-in-out">
                                <td class="py-4 px-6 whitespace-nowrap text-sm text-gray-900">{{ $leaveType->leaveTypeID }}</td>
                                <td class="py-4 px-6 whitespace-nowrap text-sm text-gray-900">{{ $leaveType->name }}</td>
                                <td class="py-4 px-6 text-sm text-gray-500 max-w-xs truncate">{{ $leaveType->description }}</td>
                                <td class="py-4 px-6 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $leaveType->isPaid ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $leaveType->isPaid ? 'Yes' : 'No' }}
                                    </span>
                                </td>
                                <td class="py-4 px-6 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $leaveType->requiresApproval ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $leaveType->requiresApproval ? 'Yes' : 'No' }}
                                    </span>
                                </td>
                                <td class="py-4 px-6 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $leaveType->isActive ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $leaveType->isActive ? 'Yes' : 'No' }}
                                    </span>
                                </td>
                                <td class="py-4 px-6 whitespace-nowrap text-sm text-gray-500">{{ $leaveType->leavePolicyID ?? 'N/A' }}</td>
                                <!-- Add more cells for other fields -->
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination Links -->
            <div class="mt-8">
                {{ $leaveTypes->links() }}
            </div>
        @endif

        <!-- Initialize Yearly Balances Button -->
        <div class="flex justify-start mt-8">
            <button class="bg-purple-600 hover:bg-purple-700 text-white font-semibold py-2 px-6 rounded-lg shadow-md transition duration-300 ease-in-out transform hover:scale-105">
                Initialize Yearly Balances
            </button>
        </div>

        <!-- Today's Leave Requests Section -->
        <h2 class="text-2xl font-bold text-gray-800 mt-12 mb-6 border-b pb-4">Today's Leave Requests</h2>

        @if ($todaysLeaveRequests->isEmpty())
            <p class="text-center text-gray-600 py-8 text-lg">No leave requests found for today.</p>
        @else
            <div class="overflow-x-auto rounded-lg border border-gray-200 shadow-sm">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="py-3 px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employee</th>
                            <th class="py-3 px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Leave Type</th>
                            <th class="py-3 px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Start Date</th>
                            <th class="py-3 px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">End Date</th>
                            <th class="py-3 px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Days</th>
                            <th class="py-3 px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Duration Type</th>
                            <th class="py-3 px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="py-3 px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reason</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($todaysLeaveRequests as $request)
                            <tr class="hover:bg-gray-100 transition duration-150 ease-in-out">
                                <td class="py-4 px-6 whitespace-nowrap text-sm text-gray-900">{{ $request->employee->firstName ?? 'N/A' }} {{ $request->employee->lastName ?? '' }}</td>
                                <td class="py-4 px-6 whitespace-nowrap text-sm text-gray-900">{{ $request->leaveType->name ?? 'N/A' }}</td>
                                <td class="py-4 px-6 whitespace-nowrap text-sm text-gray-500">{{ $request->startDate->format('Y-m-d') }}</td>
                                <td class="py-4 px-6 whitespace-nowrap text-sm text-gray-500">{{ $request->endDate->format('Y-m-d') }}</td>
                                <td class="py-4 px-6 whitespace-nowrap text-sm text-gray-900">{{ $request->requestedDays }}</td>
                                <td class="py-4 px-6 whitespace-nowrap text-sm text-gray-500">{{ $request->durationType }}</td>
                                <td class="py-4 px-6 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                        @if ($request->status === 'Pending') bg-yellow-100 text-yellow-800
                                        @elseif ($request->status === 'Approved') bg-green-100 text-green-800
                                        @elseif ($request->status === 'Rejected') bg-red-100 text-red-800
                                        @else bg-gray-100 text-gray-800 @endif">
                                        {{ $request->status }}
                                    </span>
                                </td>
                                <td class="py-4 px-6 text-sm text-gray-500 max-w-xs truncate">{{ $request->reason }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

</body>
</html>
