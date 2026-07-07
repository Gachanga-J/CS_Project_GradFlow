<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Create User') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">

                    <h3 class="text-lg font-semibold text-gray-700 mb-2">
                        New Department Coordinator or System Administrator
                    </h3>
                    <p class="text-xs text-gray-400 mb-6">
                        A temporary password will be generated and shown once after creation. Students and Supervisors are not created here.
                    </p>

                    <form method="POST" action="{{ route('system-administrator.users.store') }}">
                        @csrf

                        {{-- First Name --}}
                        <div class="mb-4">
                            <label for="first_name" class="block text-sm font-medium text-gray-700 mb-1">
                                First Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text"
                                   id="first_name"
                                   name="first_name"
                                   value="{{ old('first_name') }}"
                                   class="w-full border-gray-300 rounded-md shadow-sm focus:ring focus:ring-indigo-200 @error('first_name') border-red-500 @enderror">
                            @error('first_name')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Last Name --}}
                        <div class="mb-4">
                            <label for="last_name" class="block text-sm font-medium text-gray-700 mb-1">
                                Last Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text"
                                   id="last_name"
                                   name="last_name"
                                   value="{{ old('last_name') }}"
                                   class="w-full border-gray-300 rounded-md shadow-sm focus:ring focus:ring-indigo-200 @error('last_name') border-red-500 @enderror">
                            @error('last_name')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Role --}}
                        <div class="mb-4">
                            <label for="role" class="block text-sm font-medium text-gray-700 mb-1">
                                Role <span class="text-red-500">*</span>
                            </label>
                            <select id="role"
                                    name="role"
                                    onchange="document.getElementById('department-field').style.display = this.value === 'department_coordinator' ? 'block' : 'none';"
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring focus:ring-indigo-200 @error('role') border-red-500 @enderror">
                                <option value="">-- Select Role --</option>
                                <option value="department_coordinator" {{ old('role') === 'department_coordinator' ? 'selected' : '' }}>Department Coordinator</option>
                                <option value="system_administrator" {{ old('role') === 'system_administrator' ? 'selected' : '' }}>System Administrator</option>
                            </select>
                            @error('role')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Department (shown only for Department Coordinator) --}}
                        <div id="department-field" class="mb-6" style="display: {{ old('role') === 'department_coordinator' ? 'block' : 'none' }};">
                            <label for="department_id" class="block text-sm font-medium text-gray-700 mb-1">
                                Department <span class="text-red-500">*</span>
                            </label>
                            <select id="department_id"
                                    name="department_id"
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring focus:ring-indigo-200 @error('department_id') border-red-500 @enderror">
                                <option value="">-- Select Department --</option>
                                @foreach ($departments as $department)
                                    <option value="{{ $department->id }}" {{ (string) old('department_id') === (string) $department->id ? 'selected' : '' }}>
                                        {{ $department->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('department_id')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Buttons --}}
                        <div class="flex items-center justify-end gap-4">
                            <a href="{{ route('system-administrator.users.index') }}"
                               style="font-size:14px; font-weight:600; padding:8px 20px; border-radius:6px; border:2px solid #6b7280; background:white; color:#374151; text-decoration:none;">
                                Cancel
                            </a>
                            <button type="submit"
                                    style="font-size:14px; font-weight:600; padding:8px 20px; border-radius:6px; border:2px solid #3730a3; background:#4f46e5; color:white; cursor:pointer;">
                                Create User
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>