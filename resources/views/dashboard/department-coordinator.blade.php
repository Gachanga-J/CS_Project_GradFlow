<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Department Coordinator Dashboard') }}
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
                        <li><strong>{{ __('Department') }}:</strong> {{ auth()->user()->department->name ?? __('Not assigned') }}</li>
                        <li><strong>{{ __('Role') }}:</strong> {{ ucfirst(str_replace('_', ' ', auth()->user()->role)) }}</li>
                    </ul>
                </div>
            </div>

            {{-- Quick Stats --}}
            @php
                $deptId = auth()->user()->department_id;

                // Get distinct cohorts in this department
                $cohortYears = \App\Models\Student::whereHas('user', fn($q) => $q->where('department_id', $deptId))
                    ->whereNotNull('intake_year')
                    ->distinct()
                    ->orderByDesc('intake_year')
                    ->pluck('intake_year');

                // Scope stats to all cohorts in this department
                $deptMilestoneIds = \App\Models\Milestone::where('department_id', $deptId)->pluck('id');

                $pendingSubmissions = \App\Models\MilestoneSubmission::whereIn('milestone_id', $deptMilestoneIds)
                    ->where('status', 'supervisor_approved')
                    ->where('is_latest', true)
                    ->count();

                $totalMilestones = $deptMilestoneIds->count();
                $totalCohorts    = $cohortYears->count();
            @endphp

            {{-- Cohort pills --}}
            @if($cohortYears->isNotEmpty())
                <div class="flex flex-wrap gap-2">
                    <span class="text-xs font-semibold text-gray-500 dark:text-gray-400 self-center">Active cohorts:</span>
                    @foreach($cohortYears as $year)
                        <span class="text-xs font-bold px-3 py-1 rounded-full bg-indigo-100 text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-700">
                            {{ $year }} Cohort
                        </span>
                    @endforeach
                </div>
            @endif

            <div style="display:flex; gap:16px;">

                {{-- Pending Submissions Card --}}
                <div class="bg-white shadow-sm sm:rounded-lg p-6" style="flex:1; display:flex; align-items:center; gap:16px;">
                    <div style="background:#fef9c3; border-radius:12px; padding:12px; display:flex; align-items:center; justify-content:center;">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#ca8a04" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" xmlns="http://www.w3.org/2000/svg">
                            <path d="M5 2h14"/>
                            <path d="M5 22h14"/>
                            <path d="M17 2v4.5a2 2 0 0 1-.586 1.414L12 12l4.414 4.086A2 2 0 0 1 17 17.5V22"/>
                            <path d="M7 2v4.5a2 2 0 0 0 .586 1.414L12 12 7.586 16.086A2 2 0 0 0 7 17.5V22"/>
                        </svg>
                    </div>
                    <div>
                        <p style="font-size:28px; font-weight:700; color:#ca8a04;">{{ $pendingSubmissions }}</p>
                        <p style="font-size:13px; color:#6b7280;">Submissions Awaiting Grading</p>
                    </div>
                </div>

                {{-- Total Milestones Card --}}
                <div class="bg-white shadow-sm sm:rounded-lg p-6" style="flex:1; display:flex; align-items:center; gap:16px;">
                    <div style="background:#ede9fe; border-radius:12px; padding:12px; display:flex; align-items:center; justify-content:center;">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#4f46e5" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" xmlns="http://www.w3.org/2000/svg">
                            <path d="M5 22V4"/>
                            <path d="M5 4h12l-3 5 3 5H5"/>
                        </svg>
                    </div>
                    <div>
                        <p style="font-size:28px; font-weight:700; color:#4f46e5;">{{ $totalMilestones }}</p>
                        <p style="font-size:13px; color:#6b7280;">Total Milestones
                            @if($totalCohorts > 0)
                                <span style="font-size:11px; color:#9ca3af;">across {{ $totalCohorts }} cohort{{ $totalCohorts > 1 ? 's' : '' }}</span>
                            @endif
                        </p>
                    </div>
                </div>

                {{-- Cohorts Card --}}
                <div class="bg-white shadow-sm sm:rounded-lg p-6" style="flex:1; display:flex; align-items:center; gap:16px;">
                    <div style="background:#dcfce7; border-radius:12px; padding:12px; display:flex; align-items:center; justify-content:center;">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="4" width="18" height="18" rx="2"/>
                            <line x1="16" y1="2" x2="16" y2="6"/>
                            <line x1="8" y1="2" x2="8" y2="6"/>
                            <line x1="3" y1="10" x2="21" y2="10"/>
                        </svg>
                    </div>
                    <div>
                        <p style="font-size:28px; font-weight:700; color:#16a34a;">{{ $totalCohorts }}</p>
                        <p style="font-size:13px; color:#6b7280;">Active Cohort{{ $totalCohorts !== 1 ? 's' : '' }}</p>
                    </div>
                </div>

            </div>

            {{-- Quick Actions --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 style="font-size:16px; font-weight:600; color:#1f2937; margin-bottom:16px;">Quick Actions</h3>
                    <div style="display:flex; gap:12px;">

                        <a href="{{ route('department-coordinator.submissions.index') }}"
                           style="flex:1; display:flex; align-items:center; justify-content:center; gap:8px; background:#eab308; color:white; font-weight:600; font-size:14px; padding:12px 20px; border-radius:6px; text-decoration:none;">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M5 2h14"/>
                                <path d="M5 22h14"/>
                                <path d="M17 2v4.5a2 2 0 0 1-.586 1.414L12 12l4.414 4.086A2 2 0 0 1 17 17.5V22"/>
                                <path d="M7 2v4.5a2 2 0 0 0 .586 1.414L12 12 7.586 16.086A2 2 0 0 0 7 17.5V22"/>
                            </svg>
                            Review Submissions
                            @if ($pendingSubmissions > 0)
                                <span style="background:white; color:#ca8a04; font-size:11px; font-weight:700; padding:2px 8px; border-radius:999px; margin-left:4px;">
                                    {{ $pendingSubmissions }}
                                </span>
                            @endif
                        </a>

                        <a href="{{ route('department-coordinator.milestones.index') }}"
                           style="flex:1; display:flex; align-items:center; justify-content:center; gap:8px; background:#4f46e5; color:white; font-weight:600; font-size:14px; padding:12px 20px; border-radius:6px; text-decoration:none;">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M5 22V4"/>
                                <path d="M5 4h12l-3 5 3 5H5"/>
                            </svg>
                            Manage Milestones
                        </a>

                        <a href="{{ route('department-coordinator.milestones.create') }}"
                           style="flex:1; display:flex; align-items:center; justify-content:center; gap:8px; background:#16a34a; color:white; font-weight:600; font-size:14px; padding:12px 20px; border-radius:6px; text-decoration:none;">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10"/>
                                <path d="M12 8v8M8 12h8"/>
                            </svg>
                            New Milestone
                        </a>

                        <a href="{{ route('department-coordinator.reports.index') }}"
                           style="flex:1; display:flex; align-items:center; justify-content:center; gap:8px; background:#0891b2; color:white; font-weight:600; font-size:14px; padding:12px 20px; border-radius:6px; text-decoration:none;">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="18" y1="20" x2="18" y2="10"/>
                                <line x1="12" y1="20" x2="12" y2="4"/>
                                <line x1="6" y1="20" x2="6" y2="14"/>
                            </svg>
                            View Reports
                        </a>

                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
