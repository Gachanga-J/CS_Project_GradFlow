<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">My Project</h2>
    </x-slot>

    <div class="py-8 px-4 sm:px-6 lg:px-8 max-w-2xl mx-auto space-y-6">

        @if(session('success'))
            <div class="rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 text-sm">
                {{ session('success') }}
            </div>
        @endif

        @if(session('info'))
            <div class="rounded-lg bg-blue-50 border border-blue-200 text-blue-700 px-4 py-3 text-sm">
                {{ session('info') }}
            </div>
        @endif

        {{-- Project card --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-start justify-between gap-4 mb-4">
                <div>
                    <h3 class="text-base font-bold text-gray-800 dark:text-gray-100">{{ $project->title }}</h3>
                    <p class="text-xs text-gray-400 mt-0.5">Submitted {{ $project->created_at->format('d M Y') }}</p>
                </div>
                <span class="text-xs px-2.5 py-1 rounded-full font-semibold
                    {{ $project->status === 'active' ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-500' }}">
                    {{ ucfirst($project->status) }}
                </span>
            </div>

            {{-- Tags --}}
            @if($project->tags->isNotEmpty())
                <div class="flex flex-wrap gap-1.5 mb-4">
                    @foreach($project->tags as $tag)
                        <span class="text-xs px-2 py-0.5 rounded-full bg-cyan-50 text-cyan-700 dark:bg-cyan-900/30 dark:text-cyan-300 border border-cyan-200 dark:border-cyan-700">
                            {{ $tag->name }}
                        </span>
                    @endforeach
                </div>
            @else
                <p class="text-xs text-gray-400 italic mb-4">No research tags selected.</p>
            @endif

            {{-- Supervisor status --}}
            @if($project->supervisor)
                <div class="p-4 rounded-lg bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-700">
                    <p class="text-xs font-semibold text-emerald-600 dark:text-emerald-400 uppercase tracking-wide mb-1">Supervisor Assigned</p>
                    <p class="text-sm font-medium text-emerald-800 dark:text-emerald-200">
                        {{ $project->supervisor->user->full_name }}
                    </p>
                    @if($project->supervisor->tags->isNotEmpty())
                        <div class="flex flex-wrap gap-1 mt-2">
                            @foreach($project->supervisor->tags as $tag)
                                <span class="text-xs px-1.5 py-0.5 rounded bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300
                                    {{ $project->tags->contains($tag) ? 'ring-1 ring-emerald-400' : 'opacity-60' }}">
                                    {{ $tag->name }}
                                </span>
                            @endforeach
                        </div>
                    @endif
                </div>
            @else
                <div class="p-4 rounded-lg bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-700">
                    <p class="text-xs font-semibold text-amber-600 dark:text-amber-400 uppercase tracking-wide mb-1">Awaiting Supervisor Assignment</p>
                    <p class="text-xs text-amber-700 dark:text-amber-300">
                        Your department coordinator is reviewing supervisor matches and will assign one shortly.
                    </p>
                    <a href="{{ route('student.supervisor-matches') }}"
                       class="inline-block mt-2 text-xs font-medium text-amber-700 dark:text-amber-300 underline hover:no-underline">
                        View supervisor matches →
                    </a>
                </div>
            @endif
        </div>

        <a href="{{ route('dashboard.student') }}"
           class="text-sm text-indigo-600 hover:text-indigo-800 dark:text-indigo-400">
            ← Back to Dashboard
        </a>
    </div>
</x-app-layout>
