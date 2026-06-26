<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Research Tags</h2>
    </x-slot>

    <div class="py-8 px-4 sm:px-6 lg:px-8 max-w-3xl mx-auto space-y-6">

        @if(session('success'))
            <div class="rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 text-sm dark:bg-emerald-900/30 dark:border-emerald-700 dark:text-emerald-300">
                {{ session('success') }}
            </div>
        @endif

        {{-- Create new tag --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <h3 class="text-sm font-semibold text-gray-800 dark:text-gray-100 mb-4">Add New Tag</h3>
            <form method="POST" action="{{ route('system-administrator.tags.store') }}" class="flex gap-3">
                @csrf
                <input type="text" name="name" placeholder="e.g. Machine Learning" required
                    class="flex-1 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                @error('name')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                <button type="submit"
                    class="px-4 py-2 text-sm font-medium rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white transition-colors shrink-0">
                    Add Tag
                </button>
            </form>
        </div>

        {{-- Tags list --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                <p class="text-sm font-semibold text-gray-800 dark:text-gray-100">All Tags <span class="text-gray-400 font-normal">({{ $tags->count() }})</span></p>
            </div>

            @if($tags->isEmpty())
                <p class="px-6 py-8 text-sm text-gray-400 dark:text-gray-500 text-center">No tags yet. Add one above.</p>
            @else
                <ul class="divide-y divide-gray-100 dark:divide-gray-700">
                    @foreach($tags as $tag)
                        <li class="px-6 py-3 flex items-center gap-4">
                            {{-- Inline edit form --}}
                            <form method="POST" action="{{ route('system-administrator.tags.update', $tag) }}"
                                  class="flex-1 flex items-center gap-3">
                                @csrf
                                @method('PUT')
                                <input type="text" name="name" value="{{ $tag->name }}" required
                                    class="flex-1 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500 py-1">
                                <button type="submit"
                                    class="text-xs px-3 py-1 rounded-lg bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 font-medium transition-colors">
                                    Save
                                </button>
                            </form>

                            <div class="flex items-center gap-3 shrink-0 text-xs text-gray-400">
                                <span>{{ $tag->supervisors_count }} supervisors</span>
                                <span>{{ $tag->projects_count }} projects</span>
                            </div>

                            {{-- Delete --}}
                            <form method="POST" action="{{ route('system-administrator.tags.destroy', $tag) }}"
                                  onsubmit="return confirm('Delete this tag? It will be removed from all supervisors and projects.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="text-xs px-3 py-1 rounded-lg bg-red-50 hover:bg-red-100 dark:bg-red-900/30 dark:hover:bg-red-900/50 text-red-600 dark:text-red-400 font-medium transition-colors">
                                    Delete
                                </button>
                            </form>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>
</x-app-layout>
