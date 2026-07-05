<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Student Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @php
                $student    = auth()->user()->student;
                $milestones = \App\Models\Milestone::where('department_id', auth()->user()->department_id)
                                ->where('intake_year',   $student->intake_year)
                                ->where('year_of_study', $student->year_of_study)
                                ->orderBy('sequence_order')
                                ->get();

                // Pre-load all submissions for this student in one query
                $submissionMap = \App\Models\MilestoneSubmission::where('student_id', $student->id)
                    ->where('is_latest', true)
                    ->get()
                    ->keyBy('milestone_id');

                // Progress counts
                $totalMilestones = $milestones->count();
                $submittedCount  = $submissionMap->count();
                $gradedCount     = $submissionMap->filter(fn($s) => $s->status === 'graded')->count();
                $progressPercent = $totalMilestones > 0 ? round(($submittedCount / $totalMilestones) * 100) : 0;

                // Urgent milestones (deadline within 3 days, not yet submitted)
                $urgentMilestones = $milestones->filter(function ($m) use ($submissionMap) {
                    if ($m->status !== 'open' || !$m->deadline) return false;
                    $daysLeft = (int) now()->startOfDay()->diffInDays($m->deadline, false);
                    if ($daysLeft > 3 || $daysLeft < 0) return false;
                    return ! $submissionMap->has($m->id);
                });

                // Overdue milestones (deadline passed, not yet submitted)
                $overdueMilestones = $milestones->filter(function ($m) use ($submissionMap) {
                    if ($m->status !== 'open' || !$m->deadline) return false;
                    $daysLeft = (int) now()->startOfDay()->diffInDays($m->deadline, false);
                    if ($daysLeft >= 0) return false;
                    return ! $submissionMap->has($m->id);
                });
            @endphp

            {{-- Overdue Banner --}}
            @if($overdueMilestones->count() > 0)
                <div class="flex items-start gap-3 p-4 bg-red-50 border border-red-300 rounded-lg">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mt-0.5 shrink-0">
                        <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
                    </svg>
                    <div>
                        <p class="text-sm font-semibold text-red-700">
                            {{ $overdueMilestones->count() }} overdue milestone{{ $overdueMilestones->count() > 1 ? 's' : '' }}!
                        </p>
                        <p class="text-xs text-red-600 mt-0.5">
                            {{ $overdueMilestones->pluck('title')->join(', ') }} — please submit as soon as possible.
                        </p>
                    </div>
                </div>
            @endif

            {{-- Urgent Banner --}}
            @if($urgentMilestones->count() > 0)
                <div class="flex items-start gap-3 p-4 bg-orange-50 border border-orange-300 rounded-lg">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#ea580c" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mt-0.5 shrink-0">
                        <path d="M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
                        <line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/>
                    </svg>
                    <div>
                        <p class="text-sm font-semibold text-orange-700">Deadline approaching!</p>
                        <p class="text-xs text-orange-600 mt-0.5">
                            {{ $urgentMilestones->map(function($m) {
                                $days = (int) now()->startOfDay()->diffInDays($m->deadline, false);
                                return $m->title . ' (' . ($days === 0 ? 'due today' : "in {$days} day" . ($days > 1 ? 's' : '')) . ')';
                            })->join(', ') }}
                        </p>
                    </div>
                </div>
            @endif

            {{-- Welcome Card --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <p class="text-lg font-semibold">
                        {{ __('Welcome back, :name!', ['name' => auth()->user()->full_name]) }}
                    </p>
                    <ul class="mt-4 space-y-1 text-sm text-gray-600 dark:text-gray-400">
                        <li><strong>{{ __('Email') }}:</strong> {{ auth()->user()->email }}</li>
                        <li><strong>{{ __('Department') }}:</strong> {{ auth()->user()->department->name ?? __('Not assigned') }}</li>
                        <li><strong>{{ __('Registration Number') }}:</strong> {{ $student->reg_number }}</li>
                        <li><strong>{{ __('Year of Study') }}:</strong> {{ $student->year_of_study ?? __('Not set') }}</li>
                        <li><strong>{{ __('Cohort') }}:</strong> {{ $student->intake_year ?? __('Not set') }}</li>
                    </ul>
                </div>
            </div>

            {{-- Progress Bar Card --}}
            @if($totalMilestones > 0)
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-3">
                            <h3 class="text-sm font-semibold text-gray-800 dark:text-gray-100">Overall Progress</h3>
                            <span class="text-sm font-bold
                                {{ $progressPercent === 100 ? 'text-green-600' : ($progressPercent >= 50 ? 'text-indigo-600' : 'text-gray-500') }}">
                                {{ $progressPercent }}%
                            </span>
                        </div>

                        <div class="w-full bg-gray-100 dark:bg-gray-700 rounded-full h-3 overflow-hidden">
                            <div class="h-3 rounded-full transition-all duration-500
                                {{ $progressPercent === 100 ? 'bg-green-500' : ($overdueMilestones->count() > 0 ? 'bg-red-500' : ($urgentMilestones->count() > 0 ? 'bg-orange-500' : 'bg-indigo-500')) }}"
                                 style="width: {{ $progressPercent }}%">
                            </div>
                        </div>

                        <div class="flex items-center gap-6 mt-4">
                            <div class="flex items-center gap-1.5">
                                <span class="w-2.5 h-2.5 rounded-full bg-indigo-500"></span>
                                <span class="text-xs text-gray-500">{{ $submittedCount }} submitted</span>
                            </div>
                            <div class="flex items-center gap-1.5">
                                <span class="w-2.5 h-2.5 rounded-full bg-green-500"></span>
                                <span class="text-xs text-gray-500">{{ $gradedCount }} graded</span>
                            </div>
                            <div class="flex items-center gap-1.5">
                                <span class="w-2.5 h-2.5 rounded-full bg-gray-300"></span>
                                <span class="text-xs text-gray-500">{{ $totalMilestones - $submittedCount }} remaining</span>
                            </div>
                            @if($overdueMilestones->count() > 0)
                                <div class="flex items-center gap-1.5">
                                    <span class="w-2.5 h-2.5 rounded-full bg-red-500"></span>
                                    <span class="text-xs text-red-600 font-medium">{{ $overdueMilestones->count() }} overdue</span>
                                </div>
                            @endif
                        </div>

                        @if($progressPercent === 100)
                            <p class="mt-3 text-xs font-medium text-green-600">
                                🎉 All milestones submitted!
                            </p>
                        @endif
                    </div>
                </div>
            @endif

            {{-- Milestones Card --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">
                        My Milestones
                    </h3>

                    @if ($milestones->isEmpty())
                        <p class="text-sm text-gray-400 italic">
                            No milestones have been posted for your cohort and year yet. Check back later.
                        </p>
                    @else
                        <div class="space-y-3">
                            @foreach ($milestones as $milestone)
                                @php
                                    $submission = $submissionMap->get($milestone->id);

                                    $daysLeft = $milestone->deadline
                                        ? (int) now()->startOfDay()->diffInDays($milestone->deadline, false)
                                        : null;

                                    $isOverdue = $daysLeft !== null && $daysLeft < 0 && $milestone->status === 'open' && ! $submission;
                                    $isUrgent  = $daysLeft !== null && $daysLeft >= 0 && $daysLeft <= 3 && $milestone->status === 'open' && ! $submission;
                                @endphp

                                <div class="flex items-center justify-between p-4 border rounded-lg
                                    @if($isOverdue) border-red-300 bg-red-50
                                    @elseif($isUrgent) border-orange-300 bg-orange-50
                                    @elseif($submission?->status === 'graded') border-green-200 bg-green-50
                                    @elseif($submission?->status === 'supervisor_approved') border-blue-200 bg-blue-50
                                    @elseif($submission?->status === 'supervisor_rejected') border-red-200 bg-red-50
                                    @elseif($submission) border-yellow-200 bg-yellow-50
                                    @elseif($milestone->status === 'open') border-indigo-200 bg-indigo-50
                                    @else border-gray-200 bg-gray-50
                                    @endif">

                                    <div>
                                        <p class="font-medium text-gray-800 text-sm flex items-center gap-2">
                                            {{ $milestone->sequence_order }}. {{ $milestone->title }}
                                            @if($isOverdue)
                                                <span class="text-xs font-semibold text-red-600">● Overdue</span>
                                            @elseif($isUrgent)
                                                <span class="text-xs font-semibold text-orange-600">
                                                    ● {{ $daysLeft === 0 ? 'Due today' : "Due in {$daysLeft} day" . ($daysLeft > 1 ? 's' : '') }}
                                                </span>
                                            @endif
                                        </p>
                                        @if ($milestone->deadline)
                                            <p class="text-xs text-gray-400 mt-1">
                                                Deadline: {{ $milestone->deadline->format('d M Y') }}
                                            </p>
                                        @endif
                                        @if ($submission?->grade !== null)
                                            <p class="text-xs font-semibold text-green-700 mt-1">
                                                Grade: {{ $submission->grade }}/100
                                            </p>
                                        @endif
                                    </div>

                                    <div class="flex items-center gap-3">
                                        <span class="px-3 py-1 rounded-full text-xs font-medium
                                            @if($isOverdue) bg-red-100 text-red-700
                                            @elseif($isUrgent) bg-orange-100 text-orange-700
                                            @elseif($submission?->status === 'graded') bg-green-100 text-green-700
                                            @elseif($submission?->status === 'supervisor_approved') bg-blue-100 text-blue-700
                                            @elseif($submission?->status === 'supervisor_rejected') bg-red-100 text-red-700
                                            @elseif($submission) bg-yellow-100 text-yellow-700
                                            @elseif($milestone->status === 'open') bg-indigo-100 text-indigo-700
                                            @else bg-gray-100 text-gray-500
                                            @endif">
                                            @if($submission)
                                                {{ ucfirst(str_replace('_', ' ', $submission->status)) }}
                                            @elseif($isOverdue)
                                                Overdue
                                            @elseif($milestone->status === 'open')
                                                Not Submitted
                                            @else
                                                Closed
                                            @endif
                                        </span>

                                        @if ($milestone->status === 'open')
                                            <a href="{{ route('student.milestones.show', $milestone) }}"
                                               class="text-sm font-medium
                                               {{ $isOverdue ? 'text-red-600 hover:text-red-800' : ($isUrgent ? 'text-orange-600 hover:text-orange-800' : 'text-indigo-600 hover:text-indigo-800') }}">
                                                {{ $submission ? 'View' : 'Submit' }} →
                                            </a>
                                        @endif
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
