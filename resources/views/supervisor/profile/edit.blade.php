<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Research Interests</h2>
    </x-slot>

    <div class="py-8 px-4 sm:px-6 lg:px-8 max-w-2xl mx-auto">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">

            @if(session('success'))
                <div class="mb-4 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 text-sm dark:bg-emerald-900/30 dark:border-emerald-700 dark:text-emerald-300">
                    {{ session('success') }}
                </div>
            @endif

            <form method="POST" action="{{ route('supervisor.profile.update') }}" class="space-y-6">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Staff Number</label>
                        <input type="text" name="staff_number" value="{{ old('staff_number', $supervisor->staff_number) }}"
                            class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 text-sm shadow-sm focus:ring-emerald-500 focus:border-emerald-500">
                        @error('staff_number')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Max Student Capacity</label>
                        <input type="number" name="max_student_capacity" min="1" max="255"
                            value="{{ old('max_student_capacity', $supervisor->max_student_capacity) }}"
                            class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 text-sm shadow-sm focus:ring-emerald-500 focus:border-emerald-500">
                        @error('max_student_capacity')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Research Interests</label>
                    <p class="text-xs text-gray-400 dark:text-gray-500 mb-3">Select all tags that match your research areas. These are used to match you with student projects.</p>

                    @if($allTags->isEmpty())
                        <p class="text-sm text-gray-400 dark:text-gray-500 italic">No research tags available yet. Contact the system administrator.</p>
                    @else
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                            @foreach($allTags as $tag)
                                <label class="flex items-center gap-2 p-2 rounded-lg border cursor-pointer transition-colors
                                    {{ $supervisor->tags->contains($tag) ? 'border-emerald-400 bg-emerald-50 dark:bg-emerald-900/30 dark:border-emerald-600' : 'border-gray-200 dark:border-gray-600 hover:border-emerald-300 dark:hover:border-emerald-600' }}">
                                    <input type="checkbox" name="tags[]" value="{{ $tag->id }}"
                                        {{ $supervisor->tags->contains($tag) ? 'checked' : '' }}
                                        class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
                                    <span class="text-sm text-gray-700 dark:text-gray-200">{{ $tag->name }}</span>
                                </label>
                            @endforeach
                        </div>
                    @endif
                    @error('tags')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="flex justify-end">
                    <button type="submit"
                        class="px-5 py-2 text-sm font-medium rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white transition-colors">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
