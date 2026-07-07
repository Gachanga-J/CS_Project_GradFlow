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

                {{-- Departments Card --}}
                <div class="bg-white shadow-sm sm:rounded-lg p-6" style="flex:1; display:flex; align-items:center; gap:16px;">
                    <div style="background:#ede9fe; border-radius:12px; padding:12px; display:flex; align-items:center; justify-content:center;">
                        {{-- Sitemap / Org chart icon --}}
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#4f46e5" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" xmlns="http://www.w3.org/2000/svg">
                            <rect x="9" y="2" width="6" height="4" rx="1"/>
                            <rect x="2" y="18" width="6" height="4" rx="1"/>
                            <rect x="16" y="18" width="6" height="4" rx="1"/>
                            <path d="M12 6v4m0 0H5v4m7-4h7v4"/>
                        </svg>
                    </div>
                    <div>
                        <p style="font-size:28px; font-weight:700; color:#4f46e5;">{{ $totalDepartments }}</p>
                        <p style="font-size:13px; color:#6b7280;">Departments</p>
                    </div>
                </div>

                {{-- Total Users Card --}}
                <div class="bg-white shadow-sm sm:rounded-lg p-6" style="flex:1; display:flex; align-items:center; gap:16px;">
                    <div style="background:#dcfce7; border-radius:12px; padding:12px; display:flex; align-items:center; justify-content:center;">
                        {{-- People icon --}}
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" xmlns="http://www.w3.org/2000/svg">
                            <circle cx="12" cy="7" r="4"/>
                            <path d="M5.5 21a8.38 8.38 0 0 1 13 0"/>
                            <circle cx="5" cy="10" r="2.5"/>
                            <path d="M2 20a5 5 0 0 1 5.5-1.5"/>
                            <circle cx="19" cy="10" r="2.5"/>
                            <path d="M22 20a5 5 0 0 0-5.5-1.5"/>
                        </svg>
                    </div>
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
                           style="flex:1; display:flex; align-items:center; justify-content:center; gap:8px; background:#4f46e5; color:white; font-weight:600; font-size:14px; padding:12px 20px; border-radius:6px; text-decoration:none;">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="9" y="2" width="6" height="4" rx="1"/>
                                <rect x="2" y="18" width="6" height="4" rx="1"/>
                                <rect x="16" y="18" width="6" height="4" rx="1"/>
                                <path d="M12 6v4m0 0H5v4m7-4h7v4"/>
                            </svg>
                            Manage Departments
                        </a>

                        <a href="{{ route('system-administrator.users.index') }}"
                           style="flex:1; display:flex; align-items:center; justify-content:center; gap:8px; background:#0891b2; color:white; font-weight:600; font-size:14px; padding:12px 20px; border-radius:6px; text-decoration:none;">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="7" r="4"/>
                                <path d="M5.5 21a8.38 8.38 0 0 1 13 0"/>
                                <circle cx="5" cy="10" r="2.5"/>
                                <path d="M2 20a5 5 0 0 1 5.5-1.5"/>
                                <circle cx="19" cy="10" r="2.5"/>
                                <path d="M22 20a5 5 0 0 0-5.5-1.5"/>
                            </svg>
                            View Users
                        </a>

                        <a href="{{ route('system-administrator.users.create') }}"
                           style="flex:1; display:flex; align-items:center; justify-content:center; gap:8px; background:#16a34a; color:white; font-weight:600; font-size:14px; padding:12px 20px; border-radius:6px; text-decoration:none;">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="9" cy="7" r="4"/>
                                <path d="M2 21v-2a4 4 0 0 1 4-4h6a4 4 0 0 1 4 4v2"/>
                                <path d="M19 8v6M22 11h-6"/>
                            </svg>
                            New User
                        </a>

                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>