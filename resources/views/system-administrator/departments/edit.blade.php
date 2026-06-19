<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Department') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">

                    <h3 class="text-lg font-semibold text-gray-700 mb-6">
                        Edit Department
                    </h3>

                    <form method="POST" action="{{ route('system-administrator.departments.update', $department) }}">
                        @csrf
                        @method('PUT')

                        {{-- Name --}}
                        <div class="mb-6">
                            <label for="name"
                                   class="block text-sm font-medium text-gray-700 mb-1">
                                Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text"
                                   id="name"
                                   name="name"
                                   value="{{ old('name', $department->name) }}"
                                   placeholder="e.g. Department of Computing"
                                   class="w-full border-gray-300 rounded-md shadow-sm focus:ring focus:ring-indigo-200 @error('name') border-red-500 @enderror">
                            @error('name')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Buttons --}}
                        <div class="flex items-center justify-end gap-4">
                            <a href="{{ route('system-administrator.departments.index') }}"
                               style="font-size:14px; font-weight:600; padding:8px 20px; border-radius:6px; border:2px solid #6b7280; background:white; color:#374151; text-decoration:none;">
                                Cancel
                            </a>
                            <button type="submit"
                                    style="font-size:14px; font-weight:600; padding:8px 20px; border-radius:6px; border:2px solid #3730a3; background:#4f46e5; color:white; cursor:pointer;">
                                Update Department
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
