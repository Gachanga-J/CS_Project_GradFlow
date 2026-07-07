<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Assign Supervisor</h2>
    </x-slot>

    <div class="py-8 px-4 sm:px-6 lg:px-8 max-w-3xl mx-auto space-y-6">

        {{-- Project summary --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 px-6 py-4">
            <p class="text-sm font-semibold text-gray-800 dark:text-gray-100">{{ $project->title }}</p>
            <p class="text-xs text-gray-400 mt-0.5">
                {{ $project->student->user->full_name }} &middot; {{ $project->student->reg_number }}
            </p>
            @if($project->tags->isNotEmpty())
                <div class="flex flex-wrap gap-1.5 mt-3">
                    @foreach($project->tags as $tag)
                        <span class="text-xs px-2 py-0.5 rounded-full bg-cyan-50 text-cyan-700 dark:bg-cyan-900/30 dark:text-cyan-300 border border-cyan-200 dark:border-cyan-700">
                            {{ $tag->name }}
                        </span>
                    @endforeach
                </div>
            @else
                <p class="text-xs text-gray-400 mt-2 italic">No tags on this project — all supervisors show 0% compatibility.</p>
            @endif
        </div>

        {{-- Ranked supervisors --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                <p class="text-sm font-semibold text-gray-800 dark:text-gray-100">Available Supervisors</p>
                <p class="text-xs text-gray-400 mt-0.5">Ranked by tag compatibility score. Supervisors at full capacity are shown but cannot be assigned.</p>
            </div>

            @if($supervisors->isEmpty())
                <p class="px-6 py-8 text-sm text-gray-400 dark:text-gray-500 text-center">No active supervisors found.</p>
            @else
                <ul class="divide-y divide-gray-100 dark:divide-gray-700">
                    @foreach($supervisors as $supervisor)
                        <li class="px-6 py-4 flex items-center gap-4">
                            {{-- Score bar --}}
                            <div class="w-16 shrink-0 text-center">
                                <p class="text-lg font-bold
                                    {{ $supervisor->compatibility >= 60 ? 'text-emerald-600 dark:text-emerald-400' : ($supervisor->compatibility >= 30 ? 'text-yellow-600 dark:text-yellow-400' : 'text-gray-400 dark:text-gray-500') }}">
                                    {{ $supervisor->compatibility }}%
                                </p>
                                <p class="text-xs text-gray-400">match</p>
                            </div>

                            {{-- Info --}}
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-800 dark:text-gray-100">
                                    {{ $supervisor->user->full_name }}
                                    @if($project->supervisor_id === $supervisor->id)
                                        <span class="ml-1 text-xs text-indigo-600 dark:text-indigo-400">(current)</span>
                                    @endif
                                </p>
                                <p class="text-xs text-gray-400 mt-0.5">
                                    {{ $supervisor->current_load }}/{{ $supervisor->max_student_capacity }} students
                                    @if(!$supervisor->hasCapacity())
                                        &mdash; <span class="text-red-500 dark:text-red-400">Full</span>
                                    @endif
                                </p>

                                @if($supervisor->tags->isNotEmpty())
                                    <div class="flex flex-wrap gap-1 mt-1.5">
                                        @foreach($supervisor->tags as $tag)
                                            <span class="text-xs px-1.5 py-0.5 rounded bg-emerald-50 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300
                                                {{ $project->tags->contains($tag) ? 'ring-1 ring-emerald-400' : '' }}">
                                                {{ $tag->name }}
                                            </span>
                                        @endforeach
                                    </div>
                                @endif
                            </div>

                            {{-- Assign button --}}
                            @if($supervisor->hasCapacity())
                                <form method="POST" action="{{ route('department-coordinator.projects.assign.supervisor', $project) }}"
                                      onsubmit="return confirm('Assign {{ $supervisor->user->full_name }} to this project?')">
                                    @csrf
                                    <input type="hidden" name="supervisor_id" value="{{ $supervisor->id }}">
                                    <button type="submit"
                                        class="text-sm px-4 py-1.5 rounded-lg font-medium transition-colors shrink-0
                                        {{ $project->supervisor_id === $supervisor->id
                                            ? 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-300 cursor-default'
                                            : 'bg-yellow-500 hover:bg-yellow-600 text-white' }}">
                                        {{ $project->supervisor_id === $supervisor->id ? 'Assigned' : 'Assign' }}
                                    </button>
                                </form>
                            @else
                                <span class="text-xs text-gray-400 italic shrink-0">At capacity</span>
                            @endif
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>

        <div class="flex justify-start">
            <a href="{{ route('department-coordinator.projects.index') }}"
               class="text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                ← Back to Projects
            </a>
        </div>
    </div>
</x-app-layout>
