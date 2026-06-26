<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Supervisor Matches</h2>
    </x-slot>

    <div class="py-8 px-4 sm:px-6 lg:px-8 max-w-4xl mx-auto space-y-8">

        @forelse($projects as $project)
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                    <p class="text-sm font-semibold text-gray-800 dark:text-gray-100">{{ $project->title }}</p>
                    @if($project->tags->isNotEmpty())
                        <div class="flex flex-wrap gap-1.5 mt-2">
                            @foreach($project->tags as $tag)
                                <span class="text-xs px-2 py-0.5 rounded-full bg-cyan-50 text-cyan-700 dark:bg-cyan-900/30 dark:text-cyan-300 border border-cyan-200 dark:border-cyan-700">
                                    {{ $tag->name }}
                                </span>
                            @endforeach
                        </div>
                    @endif
                </div>

                @if($project->supervisor_id)
                    <div class="px-6 py-4 bg-emerald-50 dark:bg-emerald-900/20">
                        <p class="text-sm text-emerald-700 dark:text-emerald-300 font-medium">
                            Supervisor assigned: {{ $project->supervisor->user->full_name }}
                        </p>
                        <p class="text-xs text-emerald-600 dark:text-emerald-400 mt-0.5">
                            Your supervisor has been assigned by the department coordinator.
                        </p>
                    </div>
                @elseif($project->ranked_supervisors->isEmpty())
                    <p class="px-6 py-4 text-sm text-gray-400 dark:text-gray-500 italic">No supervisors available.</p>
                @else
                    <div class="divide-y divide-gray-100 dark:divide-gray-700">
                        <p class="px-6 py-3 text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wide">
                            Top Supervisor Matches
                        </p>
                        @foreach($project->ranked_supervisors as $i => $supervisor)
                            <div class="px-6 py-4 flex items-center gap-4">
                                <div class="w-12 shrink-0 text-center">
                                    <p class="text-lg font-bold
                                        {{ $supervisor->compatibility >= 60 ? 'text-emerald-600 dark:text-emerald-400' : ($supervisor->compatibility >= 30 ? 'text-yellow-600 dark:text-yellow-400' : 'text-gray-400') }}">
                                        {{ $supervisor->compatibility }}%
                                    </p>
                                    <p class="text-xs text-gray-400">#{{ $i + 1 }}</p>
                                </div>

                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-800 dark:text-gray-100">{{ $supervisor->user->full_name }}</p>
                                    <p class="text-xs text-gray-400 mt-0.5">
                                        {{ $supervisor->current_load }}/{{ $supervisor->max_student_capacity }} students
                                        @if(!$supervisor->hasCapacity())
                                            &middot; <span class="text-red-500">Full</span>
                                        @endif
                                    </p>
                                    @if($supervisor->tags->isNotEmpty())
                                        <div class="flex flex-wrap gap-1 mt-1.5">
                                            @foreach($supervisor->tags as $tag)
                                                <span class="text-xs px-1.5 py-0.5 rounded bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300
                                                    {{ $project->tags->contains($tag) ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300' : '' }}">
                                                    {{ $tag->name }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="px-6 py-3 bg-gray-50 dark:bg-gray-900/20">
                        <p class="text-xs text-gray-400 dark:text-gray-500">
                            Your department coordinator uses these rankings to assign you a supervisor. You cannot select a supervisor yourself.
                        </p>
                    </div>
                @endif
            </div>
        @empty
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 px-6 py-10 text-center">
                <p class="text-sm text-gray-400 dark:text-gray-500">You have no active projects yet. Your department coordinator will create one for you.</p>
            </div>
        @endforelse
    </div>
</x-app-layout>
