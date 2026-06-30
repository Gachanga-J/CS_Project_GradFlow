<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Students & Projects</h2>
    </x-slot>

    <div class="py-8 px-4 sm:px-6 lg:px-8 max-w-5xl mx-auto space-y-6">

        <div class="grid grid-cols-3 gap-4">
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 px-5 py-4 text-center">
                <p class="text-2xl font-bold text-emerald-600 dark:text-emerald-400">{{ $supervisor->current_load }}</p>
                <p class="text-xs text-gray-400 mt-1">Assigned Students</p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 px-5 py-4 text-center">
                <p class="text-2xl font-bold text-gray-700 dark:text-gray-200">{{ $supervisor->max_student_capacity }}</p>
                <p class="text-xs text-gray-400 mt-1">Max Capacity</p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 px-5 py-4 text-center">
                <p class="text-2xl font-bold text-{{ $supervisor->hasCapacity() ? 'emerald' : 'red' }}-600 dark:text-{{$supervisor->hasCapacity() ? 'emerald' : 'red' }}-400">
                    {{ $supervisor->hasCapacity() ? 'Open' : 'Full' }}
                </p>
                <p class="text-xs text-gray-400 mt-1">Capacity Status</p>
            </div>
        </div>

        @forelse($projects as $project)
            @php
                $milestonesTotal     = $project->milestones_total ?? 0;
                $milestonesCompleted = $project->milestones_completed ?? 0;
                $completionPercent   = $milestonesTotal > 0 ? round(($milestonesCompleted / $milestonesTotal) * 100) : 0;
            @endphp

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-sm font-semibold text-gray-800 dark:text-gray-100">{{ $project->title }}</p>
                            <p class="text-xs text-gray-400 mt-0.5">
                                {{ $project->student->user->full_name }} &middot; {{ $project->student->reg_number }}
                                @if($project->student->user->department)
                                    &middot; {{ $project->student->user->department->name }}
                                @endif
                            </p>
                        </div>
                        <span class="text-xs px-2 py-0.5 rounded-full
                            {{ $project->status === 'active' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300' : 'bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400' }}">
                            {{ ucfirst($project->status) }}
                        </span>
                    </div>

                    @if($project->tags->isNotEmpty())
                        <div class="flex flex-wrap gap-1.5 mt-3">
                            @foreach($project->tags as $tag)
                                <span class="text-xs px-2 py-0.5 rounded-full bg-cyan-50 text-cyan-700 dark:bg-cyan-900/30 dark:text-cyan-300 border border-cyan-200 dark:border-cyan-700">
                                    {{ $tag->name }}
                                </span>
                            @endforeach
                        </div>
                    @endif

                    {{-- Milestone progress mini bar --}}
                    @if($milestonesTotal > 0)
                        <div class="mt-3">
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ $milestonesCompleted }} of {{ $milestonesTotal }} milestones complete
                                </span>
                                <span class="text-xs font-semibold {{ $completionPercent === 100 ? 'text-emerald-600 dark:text-emerald-400' : 'text-gray-500 dark:text-gray-400' }}">
                                    {{ $completionPercent }}%
                                </span>
                            </div>
                            <div class="w-full bg-gray-100 dark:bg-gray-700 rounded-full h-1.5 overflow-hidden">
                                <div class="h-1.5 rounded-full transition-all duration-500 {{ $completionPercent === 100 ? 'bg-emerald-500' : 'bg-indigo-400' }}"
                                     style="width: {{ $completionPercent }}%"></div>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="px-6 py-4">
                    @if($project->next_milestone)
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            <span class="font-medium text-gray-700 dark:text-gray-200">Next milestone:</span>
                            {{ $project->next_milestone->title }}
                            @if($project->next_milestone->deadline)
                                &mdash; due {{ \Carbon\Carbon::parse($project->next_milestone->deadline)->format('d M Y') }}
                            @endif
                        </p>
                    @else
                        <p class="text-xs text-emerald-600 dark:text-emerald-400 font-medium">All milestones complete.</p>
                    @endif

                    {{-- Submission status summary --}}
                    @php
                        $submissions = $project->student->milestoneSubmissions;
                        $pendingCount  = $submissions->where('status', 'submitted')->count();
                        $approvedCount = $submissions->whereIn('status', ['supervisor_approved', 'graded'])->count();
                        $rejectedCount = $submissions->where('status', 'supervisor_rejected')->count();
                    @endphp

                    <div class="flex gap-4 mt-3 text-xs text-gray-500 dark:text-gray-400">
                        <span class="text-amber-600 dark:text-amber-400 font-medium">{{ $pendingCount }} pending</span>
                        <span class="text-emerald-600 dark:text-emerald-400 font-medium">{{ $approvedCount }} approved</span>
                        <span class="text-red-600 dark:text-red-400 font-medium">{{ $rejectedCount }} rejected</span>
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 px-6 py-10 text-center">
                <p class="text-sm text-gray-400 dark:text-gray-500">No students assigned yet.</p>
            </div>
        @endforelse
    </div>
</x-app-layout>
