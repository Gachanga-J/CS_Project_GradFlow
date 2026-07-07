<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Review Submissions</h2>
    </x-slot>

    <div class="py-8 px-4 sm:px-6 lg:px-8 max-w-5xl mx-auto space-y-8">

        @if(session('success'))
            <div class="rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 text-sm dark:bg-emerald-900/30 dark:border-emerald-700 dark:text-emerald-300">
                {{ session('success') }}
            </div>
        @endif

        @forelse($supervisor->projects as $project)
            @php
                $pending = $project->student->milestoneSubmissions->where('status', 'submitted');
                $lateCount = $project->student->milestoneSubmissions->where('submitted_late', true)->count();
            @endphp

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border overflow-hidden
                {{ $pending->count() > 0 ? 'border-amber-300 dark:border-amber-700' : 'border-gray-200 dark:border-gray-700' }}">

                {{-- Project header --}}
                <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between
                    {{ $pending->count() > 0 ? 'bg-amber-50 dark:bg-amber-900/20' : '' }}">
                    <div>
                        <p class="text-sm font-semibold text-gray-800 dark:text-gray-100">{{ $project->title }}</p>
                        <p class="text-xs text-gray-400 mt-0.5">
                            {{ $project->student->user->full_name }} · {{ $project->student->reg_number }}
                        </p>
                    </div>
                    <div class="flex items-center gap-2">
                        @if($lateCount > 0)
                            <span class="text-xs px-2.5 py-1 rounded-full font-semibold bg-orange-100 text-orange-700 dark:bg-orange-900/40 dark:text-orange-300">
                                ⚠ {{ $lateCount }} late
                            </span>
                        @endif
                        <span class="flex items-center gap-1.5 text-xs px-2.5 py-1 rounded-full font-semibold
                            {{ $pending->count() > 0 ? 'bg-amber-500 text-white' : 'bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400' }}">
                            @if($pending->count() > 0)
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
                                </svg>
                            @endif
                            {{ $pending->count() }} pending
                        </span>
                    </div>
                </div>

                @if($project->student->milestoneSubmissions->isEmpty())
                    <p class="px-6 py-4 text-sm text-gray-400 dark:text-gray-500">No submissions yet.</p>
                @else
                    <ul class="divide-y divide-gray-100 dark:divide-gray-700">
                        @foreach($project->student->milestoneSubmissions->sortByDesc('submitted_at') as $submission)
                            @php $isPending = $submission->status === 'submitted'; @endphp
                            <li class="px-6 py-4 flex items-start justify-between gap-4
                                {{ $isPending ? 'bg-amber-50/50 dark:bg-amber-900/10' : '' }}">

                                <div class="flex items-start gap-3 flex-1 min-w-0">
                                    {{-- File icon --}}
                                    <div class="shrink-0 mt-0.5 flex items-center justify-center h-9 w-9 rounded-lg
                                        {{ $isPending ? 'bg-amber-100 dark:bg-amber-900/40' : 'bg-gray-100 dark:bg-gray-700' }}">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="{{ $isPending ? '#b45309' : '#9ca3af' }}" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                            <polyline points="14 2 14 8 20 8"/>
                                            <line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/>
                                        </svg>
                                    </div>

                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <p class="text-sm font-medium text-gray-800 dark:text-gray-100">
                                                {{ $submission->milestone->title }}
                                            </p>
                                            @if($isPending)
                                                <span class="text-[10px] font-bold px-1.5 py-0.5 rounded bg-amber-500 text-white uppercase tracking-wide">New</span>
                                            @endif
                                            @if($submission->submitted_late)
                                                <span class="text-[10px] font-bold px-1.5 py-0.5 rounded bg-orange-100 text-orange-700 border border-orange-300 uppercase tracking-wide">⚠ Late</span>
                                            @endif
                                        </div>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                                            {{ $submission->file_name }}
                                            · {{ $submission->file_size_formatted }}
                                            · v{{ $submission->version_number }}
                                        </p>
                                        <p class="text-xs text-gray-400 mt-0.5">
                                            Submitted {{ $submission->submitted_at->diffForHumans() }}
                                            @if($submission->submitted_late && $submission->milestone->deadline)
                                                <span class="text-orange-500 font-medium">
                                                    ({{ round($submission->milestone->deadline->diffInDays($submission->submitted_at)) }} day{{ round($submission->milestone->deadline->diffInDays($submission->submitted_at)) > 1 ? 's' : '' }} late)
                                                </span>
                                            @endif
                                        </p>

                                        @if($submission->supervisor_feedback)
                                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 italic">
                                                Feedback: {{ $submission->supervisor_feedback }}
                                            </p>
                                        @endif
                                    </div>
                                </div>

                                <div class="flex flex-col items-end gap-2 shrink-0">
                                    {{-- Status badge --}}
                                    <span class="text-xs font-medium px-2 py-0.5 rounded-full
                                        @if($submission->status === 'submitted') bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300
                                        @elseif($submission->status === 'supervisor_approved') bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300
                                        @elseif($submission->status === 'supervisor_rejected') bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300
                                        @else bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300
                                        @endif">
                                        {{ ucfirst(str_replace('_', ' ', $submission->status)) }}
                                    </span>

                                    {{-- Download --}}
                                    <a href="{{ route('supervisor.submissions.download', $submission) }}"
                                       class="flex items-center gap-1 text-xs text-indigo-600 dark:text-indigo-400 hover:underline">
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                                            <polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/>
                                        </svg>
                                        Download
                                    </a>

                                    {{-- Approve / Reject (only for pending) --}}
                                    @if($submission->status === 'submitted')
                                        <div class="flex gap-2 mt-1">
                                            <form method="POST" action="{{ route('supervisor.submissions.approve', $submission) }}"
                                                  onsubmit="return confirm('Approve this submission?')" class="inline">
                                                @csrf
                                                <input type="hidden" name="supervisor_feedback" value="">
                                                <button type="submit"
                                                    class="text-xs px-3 py-1 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-medium">
                                                    Approve
                                                </button>
                                            </form>

                                            <button type="button"
                                                onclick="document.getElementById('reject-form-{{ $submission->id }}').classList.toggle('hidden')"
                                                class="text-xs px-3 py-1 rounded-lg bg-red-100 hover:bg-red-200 text-red-700 font-medium dark:bg-red-900/40 dark:hover:bg-red-900/60 dark:text-red-300">
                                                Reject
                                            </button>
                                        </div>

                                        <form id="reject-form-{{ $submission->id }}"
                                              method="POST" action="{{ route('supervisor.submissions.reject', $submission) }}"
                                              class="hidden mt-2 w-64 space-y-2">
                                            @csrf
                                            <textarea name="supervisor_feedback" rows="3" required
                                                placeholder="Feedback (required for rejection)..."
                                                class="w-full text-xs rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:ring-red-500 focus:border-red-500"></textarea>
                                            <button type="submit"
                                                class="w-full text-xs px-3 py-1.5 rounded-lg bg-red-600 hover:bg-red-700 text-white font-medium">
                                                Confirm Rejection
                                            </button>
                                        </form>

                                        <button type="button"
                                            onclick="document.getElementById('approve-feedback-{{ $submission->id }}').classList.toggle('hidden')"
                                            class="text-xs text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 mt-1">
                                            + Add feedback to approval
                                        </button>

                                        <form id="approve-feedback-{{ $submission->id }}"
                                              method="POST" action="{{ route('supervisor.submissions.approve', $submission) }}"
                                              class="hidden mt-2 w-64 space-y-2">
                                            @csrf
                                            <textarea name="supervisor_feedback" rows="3"
                                                placeholder="Optional feedback..."
                                                class="w-full text-xs rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:ring-emerald-500 focus:border-emerald-500"></textarea>
                                            <button type="submit"
                                                class="w-full text-xs px-3 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-medium">
                                                Approve with Feedback
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        @empty
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 px-6 py-10 text-center">
                <p class="text-sm text-gray-400 dark:text-gray-500">No students are assigned to you yet.</p>
            </div>
        @endforelse
    </div>
</x-app-layout>
