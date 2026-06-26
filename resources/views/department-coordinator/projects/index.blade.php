<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Projects</h2>
            <a href="{{ route('department-coordinator.projects.create') }}"
               class="text-sm px-4 py-2 rounded-lg bg-yellow-500 hover:bg-yellow-600 text-white font-medium transition-colors">
                New Project
            </a>
        </div>
    </x-slot>

    <div class="py-8 px-4 sm:px-6 lg:px-8 max-w-5xl mx-auto space-y-4">

        @if(session('success'))
            <div class="rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 text-sm dark:bg-emerald-900/30 dark:border-emerald-700 dark:text-emerald-300">
                {{ session('success') }}
            </div>
        @endif

        @forelse($projects as $project)
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 px-6 py-4 flex items-start justify-between gap-4">
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-gray-800 dark:text-gray-100">{{ $project->title }}</p>
                    <p class="text-xs text-gray-400 mt-0.5">
                        Student: {{ $project->student->user->full_name }} ({{ $project->student->reg_number }})
                    </p>
                    <p class="text-xs text-gray-400 mt-0.5">
                        Supervisor:
                        @if($project->supervisor)
                            {{ $project->supervisor->user->full_name }}
                        @else
                            <span class="text-amber-600 dark:text-amber-400">Unassigned</span>
                            &mdash;
                            <a href="{{ route('department-coordinator.projects.assign', $project) }}"
                               class="text-indigo-600 dark:text-indigo-400 hover:underline">Assign now</a>
                        @endif
                    </p>

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

                <div class="flex flex-col items-end gap-2 shrink-0">
                    <span class="text-xs px-2 py-0.5 rounded-full
                        {{ $project->status === 'active' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300' : 'bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400' }}">
                        {{ ucfirst($project->status) }}
                    </span>

                    @if(!$project->supervisor_id)
                        <a href="{{ route('department-coordinator.projects.assign', $project) }}"
                           class="text-xs px-3 py-1 rounded-lg bg-yellow-100 hover:bg-yellow-200 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-300 font-medium">
                            Assign Supervisor
                        </a>
                    @else
                        <a href="{{ route('department-coordinator.projects.assign', $project) }}"
                           class="text-xs px-3 py-1 rounded-lg bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-600 dark:text-gray-300 font-medium">
                            Reassign
                        </a>
                    @endif

                    <form method="POST" action="{{ route('department-coordinator.projects.destroy', $project) }}"
                          onsubmit="return confirm('Delete this project? This cannot be undone.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="text-xs px-3 py-1 rounded-lg bg-red-50 hover:bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400 font-medium">
                            Delete
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 px-6 py-10 text-center">
                <p class="text-sm text-gray-400 dark:text-gray-500">No projects yet.</p>
                <a href="{{ route('department-coordinator.projects.create') }}"
                   class="mt-3 inline-block text-sm text-indigo-600 dark:text-indigo-400 hover:underline">Create the first project</a>
            </div>
        @endforelse
    </div>
</x-app-layout>
