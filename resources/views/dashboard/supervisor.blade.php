<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Supervisor Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Welcome + Info Card --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="px-6 py-5 flex items-center justify-between gap-6">
                    <div>
                        <p class="text-lg font-bold text-gray-800 dark:text-gray-100">
                            {{ __('Welcome back, :name!', ['name' => auth()->user()->full_name]) }}
                        </p>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">{{ auth()->user()->email }}</p>
                    </div>
                    <div class="flex items-center gap-6 text-center shrink-0">
                        <div>
                            <p class="text-xs text-gray-400 uppercase tracking-wide">Staff No.</p>
                            <p class="text-sm font-semibold text-gray-700 dark:text-gray-200 mt-0.5">
                                {{ auth()->user()->supervisor->staff_number ?? '—' }}
                            </p>
                        </div>
                        <div class="w-px h-8 bg-gray-200 dark:bg-gray-600"></div>
                        <div>
                            <p class="text-xs text-gray-400 uppercase tracking-wide">Capacity</p>
                            <p class="text-sm font-semibold text-gray-700 dark:text-gray-200 mt-0.5">
                                {{ auth()->user()->supervisor->current_load }}/{{ auth()->user()->supervisor->max_student_capacity }}
                            </p>
                        </div>
                        <div class="w-px h-8 bg-gray-200 dark:bg-gray-600"></div>
                        <div>
                            {{-- Capacity fill bar --}}
                            @php
                                $cap     = auth()->user()->supervisor->max_student_capacity ?: 1;
                                $load    = auth()->user()->supervisor->current_load;
                                $capPct  = min(100, round(($load / $cap) * 100));
                            @endphp
                            <p class="text-xs text-gray-400 uppercase tracking-wide mb-1">Load</p>
                            <div class="w-24 bg-gray-100 dark:bg-gray-700 rounded-full h-2 overflow-hidden">
                                <div class="h-2 rounded-full {{ $capPct >= 90 ? 'bg-red-400' : ($capPct >= 60 ? 'bg-amber-400' : 'bg-emerald-400') }}"
                                     style="width: {{ $capPct }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Stat Cards --}}
            <div class="grid grid-cols-2 gap-4">
                <a href="{{ route('supervisor.submissions.index') }}"
                   class="bg-white dark:bg-gray-800 rounded-xl border {{ $pendingCount > 0 ? 'border-amber-300 dark:border-amber-600' : 'border-gray-200 dark:border-gray-700' }} px-5 py-4 flex items-center gap-4 hover:shadow-md transition group">
                    <div class="shrink-0 flex items-center justify-center h-12 w-12 rounded-xl {{ $pendingCount > 0 ? 'bg-amber-100 dark:bg-amber-900/40' : 'bg-gray-100 dark:bg-gray-700' }}">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="{{ $pendingCount > 0 ? '#b45309' : '#9ca3af' }}" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                            <polyline points="14 2 14 8 20 8"/>
                            <line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <p class="text-2xl font-bold {{ $pendingCount > 0 ? 'text-amber-600 dark:text-amber-400' : 'text-gray-700 dark:text-gray-200' }}">
                            {{ $pendingCount }}
                        </p>
                        <p class="text-xs text-gray-400 mt-0.5">Pending Reviews</p>
                    </div>
                    @if($pendingCount > 0)
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#b45309" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="opacity-60 group-hover:opacity-100 transition">
                            <polyline points="9 18 15 12 9 6"/>
                        </svg>
                    @endif
                </a>

                <a href="{{ route('supervisor.students.index') }}"
                   class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 px-5 py-4 flex items-center gap-4 hover:shadow-md transition group">
                    <div class="shrink-0 flex items-center justify-center h-12 w-12 rounded-xl bg-emerald-100 dark:bg-emerald-900/40">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#059669" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="7" r="4"/>
                            <path d="M5.5 21a8.38 8.38 0 0 1 13 0"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <p class="text-2xl font-bold text-emerald-600 dark:text-emerald-400">{{ $activeStudents }}</p>
                        <p class="text-xs text-gray-400 mt-0.5">Active Students</p>
                    </div>
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#9ca3af" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="opacity-60 group-hover:opacity-100 transition">
                        <polyline points="9 18 15 12 9 6"/>
                    </svg>
                </a>
            </div>

            {{-- Per-Department Student Progress --}}
            @forelse($departmentMilestones as $deptGroup)
                @if($deptGroup['student_rows']->isNotEmpty())
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">

                        {{-- Department header bar --}}
                        <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between bg-gray-50 dark:bg-gray-700/40">
                            <div class="flex items-center gap-2">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#6366f1" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="9" y="2" width="6" height="4" rx="1"/>
                                    <rect x="2" y="18" width="6" height="4" rx="1"/>
                                    <rect x="16" y="18" width="6" height="4" rx="1"/>
                                    <path d="M12 6v4m0 0H5v4m7-4h7v4"/>
                                </svg>
                                <h3 class="text-sm font-bold text-gray-800 dark:text-gray-100">
                                    {{ $deptGroup['department']->name ?? 'Unassigned Department' }}
                                </h3>
                            </div>
                            <div class="flex items-center gap-3 text-xs text-gray-400">
                                <span>{{ $deptGroup['student_rows']->count() }} student{{ $deptGroup['student_rows']->count() !== 1 ? 's' : '' }}</span>
                                <span>·</span>
                                <span>{{ $deptGroup['milestones']->count() }} milestone{{ $deptGroup['milestones']->count() !== 1 ? 's' : '' }}</span>
                            </div>
                        </div>

                        <div class="p-6">
                            @if($deptGroup['milestones']->isEmpty())
                                <p class="text-sm text-gray-400 italic text-center py-4">No milestones created for this department yet.</p>
                            @else

                                {{-- Legend --}}
                                <div class="flex flex-wrap gap-4 text-xs text-gray-500 mb-5">
                                    <div class="flex items-center gap-1.5">
                                        <span class="w-3.5 h-3.5 rounded-full bg-green-400 inline-block"></span> Graded
                                    </div>
                                    <div class="flex items-center gap-1.5">
                                        <span class="w-3.5 h-3.5 rounded-full bg-blue-400 inline-block"></span> Approved
                                    </div>
                                    <div class="flex items-center gap-1.5">
                                        <span class="w-3.5 h-3.5 rounded-full bg-yellow-400 inline-block"></span> Submitted
                                    </div>
                                    <div class="flex items-center gap-1.5">
                                        <span class="w-3.5 h-3.5 rounded-full bg-orange-400 inline-block"></span> Late
                                    </div>
                                    <div class="flex items-center gap-1.5">
                                        <span class="w-3.5 h-3.5 rounded-full bg-red-400 inline-block"></span> Rejected
                                    </div>
                                    <div class="flex items-center gap-1.5">
                                        <span class="w-3.5 h-3.5 rounded-full bg-gray-200 inline-block"></span> Not submitted
                                    </div>
                                </div>

                                {{-- Student × Milestone table --}}
                                <div class="overflow-x-auto rounded-lg border border-gray-100 dark:border-gray-700">
                                    <table class="w-full text-sm">
                                        <thead>
                                            <tr class="bg-gray-50 dark:bg-gray-700/50 border-b border-gray-100 dark:border-gray-700">
                                                <th class="text-left px-4 py-3 font-semibold text-gray-600 dark:text-gray-300 whitespace-nowrap">Student</th>
                                                <th class="text-left px-3 py-3 font-semibold text-gray-600 dark:text-gray-300 whitespace-nowrap">Reg No.</th>
                                                @foreach($deptGroup['milestones'] as $milestone)
                                                    <th class="text-center px-3 py-3 font-semibold text-gray-600 dark:text-gray-300">
                                                        <div class="whitespace-nowrap text-xs">{{ $milestone->sequence_order }}. {{ Str::limit($milestone->title, 14) }}</div>
                                                        @if($milestone->deadline)
                                                            <div class="text-xs font-normal text-gray-400 mt-0.5">{{ $milestone->deadline->format('d M') }}</div>
                                                        @endif
                                                    </th>
                                                @endforeach
                                                <th class="text-center px-4 py-3 font-semibold text-gray-600 dark:text-gray-300 whitespace-nowrap">Progress</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-50 dark:divide-gray-700">
                                            @foreach($deptGroup['student_rows'] as $row)
                                                @php
                                                    $pct = $row['total'] > 0
                                                        ? round(($row['submitted_count'] / $row['total']) * 100)
                                                        : 0;
                                                @endphp
                                                <tr class="hover:bg-indigo-50/40 dark:hover:bg-indigo-900/10 transition-colors">
                                                    <td class="px-4 py-3 font-medium text-gray-800 dark:text-gray-100 whitespace-nowrap">
                                                        {{ $row['student']->user->full_name }}
                                                    </td>
                                                    <td class="px-3 py-3 text-gray-400 dark:text-gray-500 whitespace-nowrap text-xs font-mono">
                                                        {{ $row['student']->reg_number ?? '—' }}
                                                    </td>

                                                    @foreach($row['milestone_statuses'] as $ms)
                                                        @php $sub = $ms['submission']; @endphp
                                                        <td class="px-3 py-3 text-center">
                                                            @if(!$sub)
                                                                <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-gray-100 dark:bg-gray-700 border border-gray-200 dark:border-gray-600" title="Not submitted">
                                                                    <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="#d1d5db" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                                                        <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                                                                    </svg>
                                                                </span>
                                                            @elseif($sub->status === 'graded')
                                                                <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-green-100 border border-green-300" title="Graded: {{ $sub->grade }}/100">
                                                                    <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                                                        <polyline points="20 6 9 17 4 12"/>
                                                                    </svg>
                                                                </span>
                                                            @elseif($sub->status === 'supervisor_approved')
                                                                <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-blue-100 border border-blue-300" title="Approved — awaiting coordinator grade">
                                                                    <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="#1d4ed8" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                                                        <polyline points="20 6 9 17 4 12"/>
                                                                    </svg>
                                                                </span>
                                                            @elseif($sub->status === 'supervisor_rejected')
                                                                <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-red-100 border border-red-300" title="Rejected">
                                                                    <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                                                        <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                                                                    </svg>
                                                                </span>
                                                            @elseif($ms['submitted_late'])
                                                                <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-orange-100 border border-orange-300" title="Late submission — awaiting review">
                                                                    <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="#ea580c" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                                                        <path d="M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
                                                                        <line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/>
                                                                    </svg>
                                                                </span>
                                                            @else
                                                                <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-yellow-100 border border-yellow-300" title="Submitted — awaiting your review">
                                                                    <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="#ca8a04" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                                                        <circle cx="12" cy="12" r="10"/>
                                                                        <line x1="12" y1="8" x2="12" y2="12"/>
                                                                        <line x1="12" y1="16" x2="12.01" y2="16"/>
                                                                    </svg>
                                                                </span>
                                                            @endif
                                                        </td>
                                                    @endforeach

                                                    {{-- Progress --}}
                                                    <td class="px-4 py-3">
                                                        <div class="flex items-center gap-2 min-w-28">
                                                            <div class="flex-1 bg-gray-100 dark:bg-gray-700 rounded-full h-2 overflow-hidden">
                                                                <div class="h-2 rounded-full transition-all duration-500 {{ $pct === 100 ? 'bg-green-500' : 'bg-indigo-400' }}"
                                                                     style="width: {{ $pct }}%"></div>
                                                            </div>
                                                            <span class="text-xs font-bold whitespace-nowrap {{ $pct === 100 ? 'text-green-600' : 'text-gray-500' }}">
                                                                {{ $row['submitted_count'] }}/{{ $row['total'] }}
                                                            </span>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                            @endif
                        </div>
                    </div>
                @endif
            @empty
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-12 text-center">
                    <svg class="mx-auto mb-3 text-gray-300" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="7" r="4"/><path d="M5.5 21a8.38 8.38 0 0 1 13 0"/>
                    </svg>
                    <p class="text-sm text-gray-400">No students assigned yet.</p>
                    <p class="text-xs text-gray-300 mt-1">Students will appear here once the coordinator assigns them to you.</p>
                </div>
            @endforelse

        </div>
    </div>
</x-app-layout>
