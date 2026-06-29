<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Student Progress') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div style="display:flex; justify-content:space-between; align-items:center;">
                <div>
                    <h3 class="text-lg font-semibold text-gray-800">Student Progress Table</h3>
                    <p class="text-sm text-gray-500 mt-0.5">
                        {{ $students->count() }} student{{ $students->count() !== 1 ? 's' : '' }} ·
                        {{ $milestones->count() }} milestone{{ $milestones->count() !== 1 ? 's' : '' }}
                    </p>
                </div>
                <a href="{{ route('department-coordinator.submissions.index') }}"
                   style="font-size:13px; font-weight:600; padding:8px 16px; border-radius:6px; border:2px solid #6b7280; background:white; color:#374151; text-decoration:none;">
                    ← Back to Submissions
                </a>
            </div>

            @if($students->isEmpty())
                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <p class="text-sm text-gray-400 italic">No students in this department yet.</p>
                </div>
            @elseif($milestones->isEmpty())
                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <p class="text-sm text-gray-400 italic">No milestones created yet.</p>
                </div>
            @else

                {{-- Legend --}}
                <div class="flex flex-wrap gap-4 text-xs text-gray-600">
                    <div class="flex items-center gap-1.5">
                        <span class="w-4 h-4 rounded bg-green-100 border border-green-300 inline-block"></span> Graded
                    </div>
                    <div class="flex items-center gap-1.5">
                        <span class="w-4 h-4 rounded bg-blue-100 border border-blue-300 inline-block"></span> Supervisor Approved
                    </div>
                    <div class="flex items-center gap-1.5">
                        <span class="w-4 h-4 rounded bg-yellow-100 border border-yellow-300 inline-block"></span> Submitted
                    </div>
                    <div class="flex items-center gap-1.5">
                        <span class="w-4 h-4 rounded bg-red-100 border border-red-300 inline-block"></span> Rejected
                    </div>
                    <div class="flex items-center gap-1.5">
                        <span class="w-4 h-4 rounded bg-gray-100 border border-gray-300 inline-block"></span> Not Submitted
                    </div>
                </div>

                {{-- Table --}}
                <div class="bg-white shadow-sm sm:rounded-lg overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-gray-200 bg-gray-50">
                                <th class="text-left px-4 py-3 font-semibold text-gray-700 whitespace-nowrap">Student</th>
                                <th class="text-left px-4 py-3 font-semibold text-gray-700 whitespace-nowrap">Reg No.</th>
                                @foreach($milestones as $milestone)
                                    <th class="text-center px-3 py-3 font-semibold text-gray-700">
                                        <div class="whitespace-nowrap">{{ $milestone->sequence_order }}. {{ Str::limit($milestone->title, 14) }}</div>
                                        @if($milestone->deadline)
                                            <div class="text-xs font-normal text-gray-400">{{ $milestone->deadline->format('d M') }}</div>
                                        @endif
                                    </th>
                                @endforeach
                                <th class="text-center px-4 py-3 font-semibold text-gray-700 whitespace-nowrap">Progress</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($students as $student)
                                @php
                                    $studentId          = $student->student?->id;
                                    $studentSubs        = $allSubmissions->get($studentId, collect());
                                    $submittedCount     = $studentSubs->count();
                                    $gradedCount        = $studentSubs->where('status', 'graded')->count();
                                    $progressPct        = $milestones->count() > 0
                                        ? round(($submittedCount / $milestones->count()) * 100)
                                        : 0;
                                @endphp
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-4 py-3 font-medium text-gray-800 whitespace-nowrap">
                                        {{ $student->full_name }}
                                    </td>
                                    <td class="px-4 py-3 text-gray-500 whitespace-nowrap">
                                        {{ $student->student?->reg_number ?? '—' }}
                                    </td>

                                    @foreach($milestones as $milestone)
                                        @php
                                            $sub = $studentSubs->firstWhere('milestone_id', $milestone->id);
                                        @endphp
                                        <td class="px-3 py-3 text-center">
                                            @if(!$sub)
                                                <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-gray-100 border border-gray-200" title="Not submitted">
                                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#9ca3af" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                                        <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                                                    </svg>
                                                </span>
                                            @elseif($sub->status === 'graded')
                                                <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-green-100 border border-green-300" title="Graded: {{ $sub->grade }}/100">
                                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                                        <polyline points="20 6 9 17 4 12"/>
                                                    </svg>
                                                </span>
                                            @elseif($sub->status === 'supervisor_approved')
                                                <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-blue-100 border border-blue-300" title="Supervisor approved">
                                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#1d4ed8" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                                        <polyline points="20 6 9 17 4 12"/>
                                                    </svg>
                                                </span>
                                            @elseif($sub->status === 'supervisor_rejected')
                                                <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-red-100 border border-red-300" title="Supervisor rejected">
                                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                                        <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                                                    </svg>
                                                </span>
                                            @else
                                                <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-yellow-100 border border-yellow-300" title="Submitted, awaiting grade">
                                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#ca8a04" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                                        <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
                                                    </svg>
                                                </span>
                                            @endif
                                        </td>
                                    @endforeach

                                    <td class="px-4 py-3">
                                        <div class="flex items-center gap-2 min-w-28">
                                            <div class="flex-1 bg-gray-100 rounded-full h-2 overflow-hidden">
                                                <div class="h-2 rounded-full {{ $progressPct === 100 ? 'bg-green-500' : 'bg-indigo-500' }}"
                                                     style="width: {{ $progressPct }}%"></div>
                                            </div>
                                            <span class="text-xs font-semibold whitespace-nowrap {{ $progressPct === 100 ? 'text-green-600' : 'text-gray-500' }}">
                                                {{ $submittedCount }}/{{ $milestones->count() }}
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
</x-app-layout>
