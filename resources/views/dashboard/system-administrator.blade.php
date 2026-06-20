<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('System Administrator Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Welcome Card --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <p class="text-lg font-semibold">
                        {{ __('Welcome back, :name!', ['name' => auth()->user()->full_name]) }}
                    </p>
                    <ul class="mt-4 space-y-1 text-sm text-gray-600 dark:text-gray-400">
                        <li><strong>{{ __('Email') }}:</strong> {{ auth()->user()->email }}</li>
                        <li><strong>{{ __('Role') }}:</strong> {{ ucfirst(auth()->user()->role) }}</li>
                    </ul>
                </div>
            </div>

            {{-- Quick Stats --}}
            @php
                $totalDepartments = \App\Models\Department::count();
                $totalUsers       = \App\Models\User::count();
            @endphp

            <div style="display:flex; gap:16px;">
                <div class="bg-white shadow-sm sm:rounded-lg p-6" style="flex:1; display:flex; align-items:center; gap:16px;">
                    <div style="font-size:32px; font-weight:700; color:#4f46e5;">#</div>
                    <div>
                        <p style="font-size:28px; font-weight:700; color:#4f46e5;">{{ $totalDepartments }}</p>
                        <p style="font-size:13px; color:#6b7280;">Departments</p>
                    </div>
                </div>
                <div class="bg-white shadow-sm sm:rounded-lg p-6" style="flex:1; display:flex; align-items:center; gap:16px;">
                    <div style="font-size:32px; font-weight:700; color:#16a34a;">#</div>
                    <div>
                        <p style="font-size:28px; font-weight:700; color:#16a34a;">{{ $totalUsers }}</p>
                        <p style="font-size:13px; color:#6b7280;">Total Users</p>
                    </div>
                </div>
            </div>

            {{-- Quick Actions --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 style="font-size:16px; font-weight:600; color:#1f2937; margin-bottom:16px;">Quick Actions</h3>
                    <div style="display:flex; gap:12px;">
                        <a href="{{ route('system-administrator.departments.index') }}"
                           style="flex:1; display:flex; align-items:center; justify-content:center; background:#4f46e5; color:white; font-weight:600; font-size:14px; padding:12px 20px; border-radius:6px; text-decoration:none;">
                            Manage Departments
                        </a>
                        <a href="{{ route('system-administrator.users.index') }}"
                           style="flex:1; display:flex; align-items:center; justify-content:center; background:#0891b2; color:white; font-weight:600; font-size:14px; padding:12px 20px; border-radius:6px; text-decoration:none;">
                            View Users
                        </a>
                        <a href="{{ route('system-administrator.users.create') }}"
                           style="flex:1; display:flex; align-items:center; justify-content:center; background:#16a34a; color:white; font-weight:600; font-size:14px; padding:12px 20px; border-radius:6px; text-decoration:none;">
                            + New User
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
