<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Submit Your Project</h2>
    </x-slot>

    <div class="py-8 px-4 sm:px-6 lg:px-8 max-w-2xl mx-auto">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">

            <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">
                Enter your project title and select research tags that best describe your work.
                These tags are used to match you with the most suitable supervisor.
            </p>

            <form method="POST" action="{{ route('student.projects.store') }}" class="space-y-6">
                @csrf

                {{-- Title --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Project Title <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="title" value="{{ old('title') }}" required maxlength="255"
                           placeholder="e.g. Machine Learning for Early Disease Detection"
                           class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 text-sm shadow-sm focus:ring-cyan-500 focus:border-cyan-500">
                    @error('title')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Research Tags --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Research Tags
                    </label>
                    <p class="text-xs text-gray-400 dark:text-gray-500 mb-3">
                        Select all tags that apply to your project. More accurate tags = better supervisor match.
                    </p>

                    @if($tags->isEmpty())
                        <p class="text-sm text-gray-400 italic">
                            No research tags available yet. You can still submit your project title and tags will be added later.
                        </p>
                    @else
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                            @foreach($tags as $tag)
                                <label class="flex items-center gap-2 p-2.5 rounded-lg border cursor-pointer transition-colors
                                    border-gray-200 dark:border-gray-600 hover:border-cyan-400 dark:hover:border-cyan-500
                                    has-[:checked]:border-cyan-400 has-[:checked]:bg-cyan-50 dark:has-[:checked]:bg-cyan-900/20">
                                    <input type="checkbox" name="tags[]" value="{{ $tag->id }}"
                                           {{ in_array($tag->id, old('tags', [])) ? 'checked' : '' }}
                                           class="rounded border-gray-300 text-cyan-500 focus:ring-cyan-500">
                                    <span class="text-sm text-gray-700 dark:text-gray-200">{{ $tag->name }}</span>
                                </label>
                            @endforeach
                        </div>
                    @endif

                    @error('tags')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex justify-end gap-3 pt-2">
                    <a href="{{ route('dashboard.student') }}"
                       class="px-4 py-2 text-sm rounded-lg border border-gray-300 dark:border-gray-600 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                        Cancel
                    </a>
                    <button type="submit"
                            class="px-5 py-2 text-sm font-medium rounded-lg bg-cyan-600 hover:bg-cyan-700 text-white transition-colors">
                        Submit Project
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
