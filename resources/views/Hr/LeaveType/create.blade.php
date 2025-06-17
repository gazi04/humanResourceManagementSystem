<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create New Leave Type</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">

    <div class="container mx-auto bg-white p-8 rounded-lg shadow-xl mt-8 mb-8">
        <h1 class="text-3xl font-extrabold text-gray-800 mb-6 border-b pb-4">Create New Leave Type</h1>

        <!-- Session Messages for Success/Error -->
        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6" role="alert">
                <strong class="font-bold">Success!</strong>
                <span class="block sm:inline">{{ session('success') }}</span>
                <span class="absolute top-0 bottom-0 right-0 px-4 py-3">
                    <svg class="fill-current h-6 w-6 text-green-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><title>Close</title><path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.103l-2.651 3.746a1.2 1.2 0 1 1-1.697-1.697l3.746-2.651-3.746-2.651a1.2 1.2 0 0 1 1.697-1.697L10 8.897l2.651-3.746a1.2 1.2 0 0 1 1.697 1.697L11.103 10l3.746 2.651a1.2 1.2 0 0 1 0 1.698z"/></svg>
                </span>
            </div>
        @endif

        @if (session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6" role="alert">
                <strong class="font-bold">Error!</strong>
                <span class="block sm:inline">{{ session('error') }}</span>
                <span class="absolute top-0 bottom-0 right-0 px-4 py-3">
                    <svg class="fill-current h-6 w-6 text-red-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><title>Close</title><path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.103l-2.651 3.746a1.2 1.2 0 1 1-1.697-1.697l3.746-2.651-3.746-2.651a1.2 1.2 0 0 1 1.697-1.697L10 8.897l2.651-3.746a1.2 1.2 0 0 1 1.697 1.697L11.103 10l3.746 2.651a1.2 1.2 0 0 1 0 1.698z"/></svg>
                </span>
            </div>
        @endif

        <form action="{{ route('hr.leave-type.store') }}" method="POST">
            @csrf

            <!-- Section: Leave Type Details -->
            <h2 class="text-2xl font-semibold text-gray-700 mb-4 pb-2 border-b">Leave Type Details</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Name <span class="text-red-500">*</span></label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}"
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm p-3 focus:border-blue-500 focus:ring-blue-500
                                  @error('name') border-red-500 @enderror" required>
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea name="description" id="description" rows="3"
                              class="mt-1 block w-full border-gray-300 rounded-md shadow-sm p-3 focus:border-blue-500 focus:ring-blue-500
                                     @error('description') border-red-500 @enderror">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div class="flex items-center">
                    <input type="checkbox" name="isPaid" id="isPaid" value="1" {{ old('isPaid') ? 'checked' : '' }}
                           class="h-5 w-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                    <label for="isPaid" class="ml-2 block text-sm font-medium text-gray-700">Is Paid</label>
                    @error('isPaid')
                        <p class="mt-1 text-sm text-red-600 ml-4">{{ $message }}</p>
                    @enderror
                </div>
                <div class="flex items-center">
                    <input type="checkbox" name="requiresApproval" id="requiresApproval" value="1" {{ old('requiresApproval') ? 'checked' : '' }}
                           class="h-5 w-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                    <label for="requiresApproval" class="ml-2 block text-sm font-medium text-gray-700">Requires Approval</label>
                    @error('requiresApproval')
                        <p class="mt-1 text-sm text-red-600 ml-4">{{ $message }}</p>
                    @enderror
                </div>
                <div class="flex items-center">
                    <input type="checkbox" name="isActive" id="isActive" value="1" {{ old('isActive', true) ? 'checked' : '' }}
                           class="h-5 w-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                    <label for="isActive" class="ml-2 block text-sm font-medium text-gray-700">Is Active</label>
                    @error('isActive')
                        <p class="mt-1 text-sm text-red-600 ml-4">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Section: Leave Policy Details -->
            <h2 class="text-2xl font-semibold text-gray-700 mb-4 pb-2 border-b mt-8">Leave Policy Details</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                <div>
                    <label for="annualQuota" class="block text-sm font-medium text-gray-700 mb-1">Annual Quota <span class="text-red-500">*</span></label>
                    <input type="number" name="annualQuota" id="annualQuota" value="{{ old('annualQuota') }}" min="0"
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm p-3 focus:border-blue-500 focus:ring-blue-500
                                  @error('annualQuota') border-red-500 @enderror" required>
                    @error('annualQuota')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="maxConsecutiveDays" class="block text-sm font-medium text-gray-700 mb-1">Max Consecutive Days</label>
                    <input type="number" name="maxConsecutiveDays" id="maxConsecutiveDays" value="{{ old('maxConsecutiveDays') }}" min="1"
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm p-3 focus:border-blue-500 focus:ring-blue-500
                                  @error('maxConsecutiveDays') border-red-500 @enderror">
                    @error('maxConsecutiveDays')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div class="flex items-center col-span-1">
                    <input type="checkbox" name="allowHalfDay" id="allowHalfDay" value="1" {{ old('allowHalfDay') ? 'checked' : '' }}
                           class="h-5 w-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                    <label for="allowHalfDay" class="ml-2 block text-sm font-medium text-gray-700">Allow Half Day</label>
                    @error('allowHalfDay')
                        <p class="mt-1 text-sm text-red-600 ml-4">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="probationPeriodDays" class="block text-sm font-medium text-gray-700 mb-1">Probation Period Days <span class="text-red-500">*</span></label>
                    <input type="number" name="probationPeriodDays" id="probationPeriodDays" value="{{ old('probationPeriodDays') }}" min="0"
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm p-3 focus:border-blue-500 focus:ring-blue-500
                                  @error('probationPeriodDays') border-red-500 @enderror" required>
                    @error('probationPeriodDays')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="carryOverLimit" class="block text-sm font-medium text-gray-700 mb-1">Carry Over Limit <span class="text-red-500">*</span></label>
                    <input type="number" name="carryOverLimit" id="carryOverLimit" value="{{ old('carryOverLimit') }}" min="0" step="0.01"
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm p-3 focus:border-blue-500 focus:ring-blue-500
                                  @error('carryOverLimit') border-red-500 @enderror" required>
                    @error('carryOverLimit')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div class="col-span-full">
                    <label for="restricedDays" class="block text-sm font-medium text-gray-700 mb-1">Restricted Days (JSON Array, e.g., ["2025-01-01", "2025-12-25"])</label>
                    <textarea name="restricedDays" id="restricedDays" rows="3"
                              class="mt-1 block w-full border-gray-300 rounded-md shadow-sm p-3 focus:border-blue-500 focus:ring-blue-500
                                     @error('restricedDays') border-red-500 @enderror">{{ old('restricedDays') }}</textarea>
                    @error('restricedDays')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div class="col-span-full">
                    <label for="requirenments" class="block text-sm font-medium text-gray-700 mb-1">Requirements (JSON Object, e.g., {"document": "passport copy"})</label>
                    <textarea name="requirenments" id="requirenments" rows="3"
                              class="mt-1 block w-full border-gray-300 rounded-md shadow-sm p-3 focus:border-blue-500 focus:ring-blue-500
                                     @error('requirenments') border-red-500 @enderror">{{ old('requirenments') }}</textarea>
                    @error('requirenments')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Section: Applicable Roles -->
            <h2 class="text-2xl font-semibold text-gray-700 mb-4 pb-2 border-b mt-8">Applicable Roles <span class="text-red-500">*</span></h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 mb-8">
                @php
                    $roles = ['Admin', 'Hr', 'Manager', 'Employee']; // Hardcoded roles as per discussion
                    $oldRoles = old('roles', []);
                @endphp
                @foreach ($roles as $role)
                    <div class="flex items-center">
                        <input type="checkbox" name="roles[]" id="role-{{ strtolower($role) }}" value="{{ $loop->index + 1 }}" {{ in_array($loop->index + 1, $oldRoles) ? 'checked' : '' }}
                               class="h-5 w-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                        <label for="role-{{ strtolower($role) }}" class="ml-2 block text-sm font-medium text-gray-700">{{ $role }}</label>
                    </div>
                @endforeach
            </div>
            @error('roles')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
            @error('roles.*')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror

            <!-- Form Actions -->
            <div class="flex justify-between items-center mt-8 pt-4 border-t">
                <a href="{{ route('hr.dashboard') }}"
                   class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold py-2 px-6 rounded-lg shadow-md transition duration-300 ease-in-out transform hover:scale-105">
                    Back to List
                </a>
                <button type="submit"
                        class="bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-8 rounded-lg shadow-md transition duration-300 ease-in-out transform hover:scale-105">
                    Create Leave Type
                </button>
            </div>
        </form>
    </div>

</body>
</html>
