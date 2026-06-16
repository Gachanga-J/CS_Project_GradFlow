<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Milestone Submissions') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            @if (session('success'))
                <div class="p-4 bg-green-100 text-green-700 rounded border border-green-300">
                    {{ session('success') }}
                </div>
            @endif

            @forelse ($milestones as $milestone)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">

                        {{-- Milestone Header --}}
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-800">
                                {{ $milestone->sequence_order }}. {{ $milestone->title }}
                            </h3>
                            <span style="padding:3px 10px; border-radius:999px; font-size:11px; font-weight:600;
                                {{ $milestone->status === 'open' ? 'background:#e0e7ff; color:#3730a3;' : 'background:#f3f4f6; color:#6b7280;' }}">
                                {{ ucfirst($milestone->status) }}
                            </span>
                        </div>

                        @if ($milestone->deadline)
                            <p class="text-xs text-gray-400 mb-4">
                                Deadline: {{ $milestone->deadline->format('d M Y') }}
                            </p>
                        @endif

                        {{-- Submissions --}}
                        @if ($milestone->submissions->isEmpty())
                            <p class="text-sm text-gray-400 italic">No submissions yet.</p>
                        @else
                            <div class="space-y-4">
                                @foreach ($milestone->submissions as $submission)
                                    <div class="border rounded-lg p-4
                                        @if($submission->status === 'graded') border-green-200 bg-green-50
                                        @elseif($submission->status === 'supervisor_approved') border-blue-200 bg-blue-50
                                        @elseif($submission->status === 'supervisor_rejected') border-red-200 bg-red-50
                                        @else border-yellow-200 bg-yellow-50
                                        @endif">

                                        <div class="flex items-start justify-between gap-4">
                                            <div>
                                                <p class="font-semibold text-gray-800 text-sm">
                                                    {{ $submission->student->user->full_name }}
                                                </p>
                                                <p class="text-xs text-gray-500 mt-1">
                                                    {{ $submission->student->reg_number }}
                                                </p>
                                                <p class="text-xs text-gray-400 mt-1">
                                                    Submitted: {{ $submission->submitted_at->format('d M Y, h:i A') }}
                                                    — Version {{ $submission->version_number }}
                                                </p>

                                                {{-- Supervisor Status --}}
                                                <div class="mt-2">
                                                    <span class="text-xs font-medium text-gray-500">Supervisor: </span>
                                                    <span style="padding:2px 8px; border-radius:999px; font-size:11px; font-weight:600;
                                                        {{ $submission->status === 'supervisor_approved' ? 'background:#dbeafe; color:#1d4ed8;' : ($submission->status === 'supervisor_rejected' ? 'background:#fee2e2; color:#dc2626;' : 'background:#f3f4f6; color:#6b7280;') }}">
                                                        @if($submission->status === 'supervisor_approved') Approved
                                                        @elseif($submission->status === 'supervisor_rejected') Rejected
                                                        @else Pending
                                                        @endif
                                                    </span>
                                                </div>

                                                @if ($submission->supervisor_feedback)
                                                    <p class="text-xs text-gray-500 mt-1">
                                                        Supervisor feedback: {{ $submission->supervisor_feedback }}
                                                    </p>
                                                @endif

                                                @if ($submission->grade !== null)
                                                    <p class="text-sm font-bold text-green-700 mt-2">
                                                        Grade: {{ $submission->grade }}/100
                                                    </p>
                                                @endif

                                                @if ($submission->admin_feedback)
                                                    <p class="text-xs text-gray-500 mt-1">
                                                        Your feedback: {{ $submission->admin_feedback }}
                                                    </p>
                                                @endif
                                            </div>

                                            <span style="padding:4px 12px; border-radius:999px; font-size:11px; font-weight:600; flex-shrink:0;
                                                {{ $submission->status === 'graded' ? 'background:#dcfce7; color:#15803d;' : ($submission->status === 'supervisor_approved' ? 'background:#dbeafe; color:#1d4ed8;' : ($submission->status === 'supervisor_rejected' ? 'background:#fee2e2; color:#dc2626;' : 'background:#fef9c3; color:#a16207;')) }}">
                                                {{ ucfirst(str_replace('_', ' ', $submission->status)) }}
                                            </span>
                                        </div>

                                        {{-- Actions --}}
                                        <div class="mt-4 flex flex-wrap items-center gap-3">

                                            {{-- Download --}}
                                            <a href="{{ route('admin.submissions.download', $submission) }}"
                                               style="font-size:13px; font-weight:600; padding:6px 16px; border-radius:6px; border:2px solid #6b7280; background:white; color:#374151; text-decoration:none;">
                                                Download File
                                            </a>

                                            {{-- Grade Form --}}
                                            @if ($submission->status !== 'graded')
                                                <form method="POST"
                                                      action="{{ route('admin.submissions.grade', $submission) }}"
                                                      style="display:flex; flex-wrap:wrap; align-items:center; gap:8px;">
                                                    @csrf
                                                    <div style="display:flex; align-items:center; gap:6px;">
                                                        <input type="number"
                                                               name="grade"
                                                               min="0"
                                                               max="100"
                                                               placeholder="0"
                                                               required
                                                               class="text-sm border-gray-300 rounded-md shadow-sm w-20">
                                                        <span class="text-sm font-medium text-gray-600">/ 100</span>
                                                    </div>
                                                    <input type="text"
                                                           name="admin_feedback"
                                                           placeholder="Feedback (optional)"
                                                           class="text-sm border-gray-300 rounded-md shadow-sm w-56">
                                                    <button type="submit"
                                                            style="font-size:13px; font-weight:600; padding:6px 16px; border-radius:6px; border:2px solid #3730a3; background:#4f46e5; color:white; cursor:pointer;">
                                                        Grade Submission
                                                    </button>
                                                </form>
                                            @else
                                                {{-- Regrade Form --}}
                                                <form method="POST"
                                                      action="{{ route('admin.submissions.grade', $submission) }}"
                                                      style="display:flex; flex-wrap:wrap; align-items:center; gap:8px;">
                                                    @csrf
                                                    <div style="display:flex; align-items:center; gap:6px;">
                                                        <input type="number"
                                                               name="grade"
                                                               min="0"
                                                               max="100"
                                                               value="{{ $submission->grade }}"
                                                               required
                                                               class="text-sm border-gray-300 rounded-md shadow-sm w-20">
                                                        <span class="text-sm font-medium text-gray-600">/ 100</span>
                                                    </div>
                                                    <input type="text"
                                                           name="admin_feedback"
                                                           value="{{ $submission->admin_feedback }}"
                                                           placeholder="Feedback (optional)"
                                                           class="text-sm border-gray-300 rounded-md shadow-sm w-56">
                                                    <button type="submit"
                                                            style="font-size:13px; font-weight:600; padding:6px 16px; border-radius:6px; border:2px solid #4b5563; background:white; color:#374151; cursor:pointer;">
                                                        Regrade Submission
                                                    </button>
                                                </form>
                                            @endif

                                        </div>

                                    </div>
                                @endforeach
                            </div>
                        @endif

                    </div>
                </div>
            @empty
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <p class="text-sm text-gray-400 italic">No milestones created yet.</p>
                    </div>
                </div>
            @endforelse

        </div>
    </div>
</x-app-layout>