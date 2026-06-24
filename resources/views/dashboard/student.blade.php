<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Student Dashboard') }}
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
                        <li><strong>{{ __('Registration Number') }}:</strong> {{ auth()->user()->student->reg_number }}</li>
                        <li><strong>{{ __('Year of Study') }}:</strong> {{ auth()->user()->student->year_of_study ?? __('Not set') }}</li>
                    </ul>
                </div>
            </div>

            {{-- Milestones Card --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">
                        My Milestones
                    </h3>

                    @php
                        $student    = auth()->user()->student;
                        $milestones = \App\Models\Milestone::where('department_id', auth()->user()->department_id)->orderBy('sequence_order')->get();
                    @endphp

                    @if ($milestones->isEmpty())
                        <p class="text-sm text-gray-400 italic">
                            No milestones have been posted yet. Check back later.
                        </p>
                    @else
                        <div class="space-y-3">
                            @foreach ($milestones as $milestone)
                                @php
                                    $submission = \App\Models\MilestoneSubmission::where('milestone_id', $milestone->id)
                                        ->where('student_id', $student->id)
                                        ->where('is_latest', true)
                                        ->first();
                                @endphp

                                <div class="flex items-center justify-between p-4 border rounded-lg
                                    @if($submission?->status === 'graded') border-green-200 bg-green-50
                                    @elseif($submission?->status === 'supervisor_approved') border-blue-200 bg-blue-50
                                    @elseif($submission?->status === 'supervisor_rejected') border-red-200 bg-red-50
                                    @elseif($submission) border-yellow-200 bg-yellow-50
                                    @elseif($milestone->status === 'open') border-indigo-200 bg-indigo-50
                                    @else border-gray-200 bg-gray-50
                                    @endif">

                                    <div>
                                        <p class="font-medium text-gray-800 text-sm">
                                            {{ $milestone->sequence_order }}. {{ $milestone->title }}
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
                                            @if($submission?->status === 'graded') bg-green-100 text-green-700
                                            @elseif($submission?->status === 'supervisor_approved') bg-blue-100 text-blue-700
                                            @elseif($submission?->status === 'supervisor_rejected') bg-red-100 text-red-700
                                            @elseif($submission) bg-yellow-100 text-yellow-700
                                            @elseif($milestone->status === 'open') bg-indigo-100 text-indigo-700
                                            @else bg-gray-100 text-gray-500
                                            @endif">
                                            @if($submission)
                                                {{ ucfirst(str_replace('_', ' ', $submission->status)) }}
                                            @elseif($milestone->status === 'open')
                                                Not Submitted
                                            @else
                                                Closed
                                            @endif
                                        </span>

                                        @if ($milestone->status === 'open')
                                            <a href="{{ route('student.milestones.show', $milestone) }}"
                                               class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">
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
