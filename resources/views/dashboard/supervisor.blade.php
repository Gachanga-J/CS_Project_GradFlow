<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Supervisor Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Welcome Card --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <p>{{ __('Welcome back, :name!', ['name' => auth()->user()->full_name]) }}</p>

                    <ul class="mt-4 space-y-1 text-sm text-gray-600 dark:text-gray-400">
                        <li><strong>{{ __('Email') }}:</strong> {{ auth()->user()->email }}</li>
                        <li><strong>{{ __('Staff Number') }}:</strong> {{ auth()->user()->supervisor->staff_number ?? __('Not set') }}</li>
                        <li><strong>{{ __('Max Student Capacity') }}:</strong> {{ auth()->user()->supervisor->max_student_capacity }}</li>
                        <li><strong>{{ __('Current Load') }}:</strong> {{ auth()->user()->supervisor->current_load }}</li>
                    </ul>
                </div>
            </div>

            {{-- Stat Cards --}}
            <div class="grid grid-cols-2 gap-4">
                <a href="{{ route('supervisor.submissions.index') }}"
                   class="bg-white dark:bg-gray-800 rounded-xl border {{ $pendingCount > 0 ? 'border-amber-300 dark:border-amber-700' : 'border-gray-200 dark:border-gray-700' }} px-5 py-4 flex items-center gap-4 hover:shadow-sm transition">
                    <div class="shrink-0 flex items-center justify-center h-11 w-11 rounded-lg {{ $pendingCount > 0 ? 'bg-amber-100 dark:bg-amber-900/40' : 'bg-gray-100 dark:bg-gray-700' }}">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="{{ $pendingCount > 0 ? '#b45309' : '#9ca3af' }}" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                            <polyline points="14 2 14 8 20 8"/>
                            <line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-2xl font-bold {{ $pendingCount > 0 ? 'text-amber-600 dark:text-amber-400' : 'text-gray-700 dark:text-gray-200' }}">{{ $pendingCount }}</p>
                        <p class="text-xs text-gray-400 mt-0.5">Pending Reviews</p>
                    </div>
                </a>

                <a href="{{ route('supervisor.students.index') }}"
                   class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 px-5 py-4 flex items-center gap-4 hover:shadow-sm transition">
                    <div class="shrink-0 flex items-center justify-center h-11 w-11 rounded-lg bg-emerald-100 dark:bg-emerald-900/40">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#059669" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="7" r="4"/>
                            <path d="M5.5 21a8.38 8.38 0 0 1 13 0"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-emerald-600 dark:text-emerald-400">{{ $activeStudents }}</p>
                        <p class="text-xs text-gray-400 mt-0.5">Active Students</p>
                    </div>
                </a>
            </div>

            {{-- Per-Milestone Submission Overview --}}
            @if($milestoneStats->isNotEmpty())
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-xl border border-gray-200 dark:border-gray-700">
                    <div class="p-6">
                        <h3 class="text-sm font-semibold text-gray-800 dark:text-gray-100 mb-4">
                            Milestone Submission Overview
                        </h3>
                        <p class="text-xs text-gray-400 mb-5">
                            Across all students you supervise
                        </p>

                        <div class="space-y-5">
                            @foreach($milestoneStats as $stat)
                                <div>
                                    <div class="flex items-center justify-between mb-1.5">
                                        <p class="text-sm font-medium text-gray-700 dark:text-gray-200">
                                            {{ $stat['milestone']->sequence_order }}. {{ $stat['milestone']->title }}
                                        </p>
                                        <p class="text-xs font-semibold {{ $stat['percent'] === 100 ? 'text-emerald-600' : 'text-gray-500' }}">
                                            {{ $stat['submitted_count'] }}/{{ $stat['submitted_count'] + $stat['not_submitted'] }} submitted
                                        </p>
                                    </div>

                                    {{-- Bar: submitted vs not submitted --}}
                                    <div class="w-full bg-gray-100 dark:bg-gray-700 rounded-full h-2.5 overflow-hidden">
                                        <div class="h-2.5 rounded-full transition-all duration-500 {{ $stat['percent'] === 100 ? 'bg-emerald-500' : 'bg-indigo-400' }}"
                                             style="width: {{ $stat['percent'] }}%"></div>
                                    </div>

                                    @if($stat['not_submitted'] > 0)
                                        <p class="text-xs text-gray-400 mt-1">
                                            {{ $stat['not_submitted'] }} {{ Str::plural('student', $stat['not_submitted']) }} yet to submit
                                        </p>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
