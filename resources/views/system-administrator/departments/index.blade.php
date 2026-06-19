<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Departments') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('success'))
                <div class="p-4 bg-green-100 text-green-700 rounded border border-green-300">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="p-4 bg-red-100 text-red-700 rounded border border-red-300">
                    {{ session('error') }}
                </div>
            @endif

            <div style="display:flex; justify-content:space-between; align-items:center;">
                <h3 class="text-lg font-semibold text-gray-800">All Departments</h3>
                <a href="{{ route('system-administrator.departments.create') }}"
                   style="display:inline-flex; align-items:center; background:#4f46e5; color:white; font-weight:600; font-size:14px; padding:10px 20px; border-radius:6px; border:2px solid #3730a3; text-decoration:none;">
                    + New Department
                </a>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">

                    @if ($departments->isEmpty())
                        <p class="text-sm text-gray-400 italic">No departments created yet.</p>
                    @else
                        <div class="space-y-3">
                            @foreach ($departments as $department)
                                <div class="flex items-start justify-between p-4 border rounded-lg border-gray-200 bg-gray-50">

                                    <div>
                                        <p class="font-semibold text-gray-800 text-sm">
                                            {{ $department->name }}
                                        </p>
                                        <p class="text-xs text-gray-400 mt-1">
                                            {{ $department->users_count }} {{ Str::plural('user', $department->users_count) }} assigned
                                        </p>
                                    </div>

                                    <div style="display:flex; align-items:center; gap:8px; flex-shrink:0; margin-left:16px;">

                                        {{-- Edit --}}
                                        <a href="{{ route('system-administrator.departments.edit', $department) }}"
                                           style="font-size:13px; font-weight:600; padding:6px 14px; border-radius:6px; border:2px solid #6b7280; background:white; color:#374151; text-decoration:none;">
                                            Edit
                                        </a>

                                        {{-- Delete --}}
                                        <form method="POST"
                                              action="{{ route('system-administrator.departments.destroy', $department) }}"
                                              onsubmit="return confirm('Delete this department?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    style="font-size:13px; font-weight:600; padding:6px 14px; border-radius:6px; border:2px solid #dc2626; background:white; color:#dc2626; cursor:pointer;">
                                                Delete
                                            </button>
                                        </form>

                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif

                </div>
            </div>

        </div>
    </div>
</x-app-layout>
