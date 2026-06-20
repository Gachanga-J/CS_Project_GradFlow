<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Create Department') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">

                    <h3 class="text-lg font-semibold text-gray-700 mb-6">
                        New Department
                    </h3>

                    <form method="POST" action="{{ route('system-administrator.departments.store') }}">
                        @csrf

                        {{-- Name --}}
                        <div class="mb-6">
                            <label for="name"
                                   class="block text-sm font-medium text-gray-700 mb-1">
                                Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text"
                                   id="name"
                                   name="name"
                                   value="{{ old('name') }}"
                                   placeholder="e.g. Department of Computing"
                                   class="w-full border-gray-300 rounded-md shadow-sm focus:ring focus:ring-indigo-200 @error('name') border-red-500 @enderror">
                            @error('name')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Create Coordinator Toggle --}}
                        <div class="mb-4 p-4 border rounded-lg border-gray-200 bg-gray-50">
                            <label style="display:flex; align-items:center; gap:8px; cursor:pointer;">
                                <input type="checkbox"
                                       id="create_coordinator"
                                       name="create_coordinator"
                                       value="1"
                                       {{ old('create_coordinator') ? 'checked' : '' }}
                                       onchange="document.getElementById('coordinator-fields').style.display = this.checked ? 'block' : 'none';">
                                <span class="text-sm font-medium text-gray-700">
                                    Also create a Department Coordinator for this department now
                                </span>
                            </label>

                            {{-- Coordinator Fields --}}
                            <div id="coordinator-fields" class="mt-4" style="display: {{ old('create_coordinator') ? 'block' : 'none' }};">
                                <p class="text-xs text-gray-400 mb-3">
                                    A temporary password will be generated and shown once after creation.
                                </p>

                                <div class="mb-3">
                                    <label for="coordinator_first_name" class="block text-sm font-medium text-gray-700 mb-1">
                                        Coordinator First Name
                                    </label>
                                    <input type="text"
                                           id="coordinator_first_name"
                                           name="coordinator_first_name"
                                           value="{{ old('coordinator_first_name') }}"
                                           class="w-full border-gray-300 rounded-md shadow-sm focus:ring focus:ring-indigo-200 @error('coordinator_first_name') border-red-500 @enderror">
                                    @error('coordinator_first_name')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="mb-1">
                                    <label for="coordinator_last_name" class="block text-sm font-medium text-gray-700 mb-1">
                                        Coordinator Last Name
                                    </label>
                                    <input type="text"
                                           id="coordinator_last_name"
                                           name="coordinator_last_name"
                                           value="{{ old('coordinator_last_name') }}"
                                           class="w-full border-gray-300 rounded-md shadow-sm focus:ring focus:ring-indigo-200 @error('coordinator_last_name') border-red-500 @enderror">
                                    @error('coordinator_last_name')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        {{-- Buttons --}}
                        <div class="flex items-center justify-end gap-4">
                            <a href="{{ route('system-administrator.departments.index') }}"
                               style="font-size:14px; font-weight:600; padding:8px 20px; border-radius:6px; border:2px solid #6b7280; background:white; color:#374151; text-decoration:none;">
                                Cancel
                            </a>
                            <button type="submit"
                                    style="font-size:14px; font-weight:600; padding:8px 20px; border-radius:6px; border:2px solid #3730a3; background:#4f46e5; color:white; cursor:pointer;">
                                Create Department
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
