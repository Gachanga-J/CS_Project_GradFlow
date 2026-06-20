<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('All Users') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">

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

            {{-- Filters + New User --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div style="display:flex; justify-content:space-between; align-items:end; gap:12px;">
                        <form method="GET" action="{{ route('system-administrator.users.index') }}" style="display:flex; gap:12px; align-items:end;">

                            <div>
                                <label for="role" class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                                <select id="role" name="role" class="border-gray-300 rounded-md shadow-sm text-sm">
                                    <option value="">All Roles</option>
                                    <option value="student" {{ request('role') === 'student' ? 'selected' : '' }}>Student</option>
                                    <option value="supervisor" {{ request('role') === 'supervisor' ? 'selected' : '' }}>Supervisor</option>
                                    <option value="department_coordinator" {{ request('role') === 'department_coordinator' ? 'selected' : '' }}>Department Coordinator</option>
                                    <option value="system_administrator" {{ request('role') === 'system_administrator' ? 'selected' : '' }}>System Administrator</option>
                                </select>
                            </div>

                            <div>
                                <label for="department_id" class="block text-sm font-medium text-gray-700 mb-1">Department</label>
                                <select id="department_id" name="department_id" class="border-gray-300 rounded-md shadow-sm text-sm">
                                    <option value="">All Departments</option>
                                    @foreach ($departments as $department)
                                        <option value="{{ $department->id }}" {{ (string) request('department_id') === (string) $department->id ? 'selected' : '' }}>
                                            {{ $department->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <button type="submit"
                                    style="font-size:13px; font-weight:600; padding:8px 16px; border-radius:6px; border:2px solid #3730a3; background:#4f46e5; color:white; cursor:pointer;">
                                Filter
                            </button>

                            @if (request('role') || request('department_id'))
                                <a href="{{ route('system-administrator.users.index') }}"
                                   style="font-size:13px; font-weight:600; padding:8px 16px; border-radius:6px; border:2px solid #6b7280; background:white; color:#374151; text-decoration:none;">
                                    Clear
                                </a>
                            @endif

                        </form>

                        <a href="{{ route('system-administrator.users.create') }}"
                           style="font-size:13px; font-weight:600; padding:8px 16px; border-radius:6px; border:2px solid #166534; background:#16a34a; color:white; text-decoration:none; white-space:nowrap;">
                            + New User
                        </a>
                    </div>
                </div>
            </div>

            {{-- Users Table --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">

                    @if ($users->isEmpty())
                        <p class="text-sm text-gray-400 italic">No users match this filter.</p>
                    @else
                        <table class="w-full text-sm">
                            <thead>
                                <tr style="border-bottom:2px solid #e5e7eb; text-align:left;">
                                    <th style="padding:8px 12px; color:#6b7280; font-weight:600;">Name</th>
                                    <th style="padding:8px 12px; color:#6b7280; font-weight:600;">Email</th>
                                    <th style="padding:8px 12px; color:#6b7280; font-weight:600;">Role</th>
                                    <th style="padding:8px 12px; color:#6b7280; font-weight:600;">Department</th>
                                    <th style="padding:8px 12px; color:#6b7280; font-weight:600;">Status</th>
                                    <th style="padding:8px 12px; color:#6b7280; font-weight:600;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($users as $user)
                                    <tr style="border-bottom:1px solid #f3f4f6;">
                                        <td style="padding:10px 12px; color:#1f2937;">
                                            {{ $user->full_name }}
                                            @if ($user->id === auth()->id())
                                                <span style="font-size:11px; color:#9ca3af;">(You)</span>
                                            @endif
                                        </td>
                                        <td style="padding:10px 12px; color:#6b7280;">{{ $user->email }}</td>
                                        <td style="padding:10px 12px;">
                                            <span style="padding:2px 10px; border-radius:999px; font-size:11px; font-weight:600; background:#e0e7ff; color:#3730a3;">
                                                {{ ucwords(str_replace('_', ' ', $user->role)) }}
                                            </span>
                                        </td>
                                        <td style="padding:10px 12px; color:#6b7280;">
                                            {{ $user->department->name ?? '—' }}
                                        </td>
                                        <td style="padding:10px 12px;">
                                            <span style="padding:2px 10px; border-radius:999px; font-size:11px; font-weight:600;
                                                {{ $user->is_active ? 'background:#dcfce7; color:#15803d;' : 'background:#fee2e2; color:#dc2626;' }}">
                                                {{ $user->is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                        </td>
                                        <td style="padding:10px 12px;">
                                            <div style="display:flex; gap:8px;">
                                                @if (in_array($user->role, ['department_coordinator', 'system_administrator']))
                                                    <a href="{{ route('system-administrator.users.edit', $user) }}"
                                                       style="font-size:12px; font-weight:600; padding:5px 12px; border-radius:6px; border:2px solid #6b7280; background:white; color:#374151; text-decoration:none;">
                                                        Edit
                                                    </a>
                                                @endif

                                                @if ($user->id !== auth()->id())
                                                    <form method="POST" action="{{ route('system-administrator.users.toggle', $user) }}">
                                                        @csrf
                                                        <button type="submit"
                                                                style="font-size:12px; font-weight:600; padding:5px 12px; border-radius:6px; cursor:pointer;
                                                                {{ $user->is_active
                                                                    ? 'border:2px solid #dc2626; background:white; color:#dc2626;'
                                                                    : 'border:2px solid #16a34a; background:white; color:#16a34a;' }}">
                                                            {{ $user->is_active ? 'Deactivate' : 'Activate' }}
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif

                </div>
            </div>

        </div>
    </div>
</x-app-layout>
