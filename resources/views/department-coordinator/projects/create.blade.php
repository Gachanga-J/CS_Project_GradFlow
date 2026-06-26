<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">New Project</h2>
    </x-slot>

    <div class="py-8 px-4 sm:px-6 lg:px-8 max-w-2xl mx-auto">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">

            <form method="POST" action="{{ route('department-coordinator.projects.store') }}" class="space-y-6">
                @csrf

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Project Title</label>
                    <input type="text" name="title" value="{{ old('title') }}" required maxlength="255"
                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 text-sm shadow-sm focus:ring-yellow-500 focus:border-yellow-500">
                    @error('title')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Student</label>
                    <select name="student_id" required
                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 text-sm shadow-sm focus:ring-yellow-500 focus:border-yellow-500">
                        <option value="">— Select a student —</option>
                        @foreach($students as $student)
                            <option value="{{ $student->id }}" {{ old('student_id') == $student->id ? 'selected' : '' }}>
                                {{ $student->user->full_name }} ({{ $student->reg_number }})
                            </option>
                        @endforeach
                    </select>
                    @error('student_id')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Research Tags</label>
                    <p class="text-xs text-gray-400 dark:text-gray-500 mb-3">Select tags that describe this project's research area. Used to match with supervisors.</p>

                    @if($tags->isEmpty())
                        <p class="text-sm text-gray-400 italic">No tags available. Ask the system administrator to add research tags first.</p>
                    @else
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                            @foreach($tags as $tag)
                                <label class="flex items-center gap-2 p-2 rounded-lg border cursor-pointer transition-colors
                                    border-gray-200 dark:border-gray-600 hover:border-yellow-400 dark:hover:border-yellow-500">
                                    <input type="checkbox" name="tags[]" value="{{ $tag->id }}"
                                        {{ in_array($tag->id, old('tags', [])) ? 'checked' : '' }}
                                        class="rounded border-gray-300 text-yellow-500 focus:ring-yellow-500">
                                    <span class="text-sm text-gray-700 dark:text-gray-200">{{ $tag->name }}</span>
                                </label>
                            @endforeach
                        </div>
                    @endif
                    @error('tags')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="flex justify-end gap-3">
                    <a href="{{ route('department-coordinator.projects.index') }}"
                       class="px-4 py-2 text-sm rounded-lg border border-gray-300 dark:border-gray-600 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                        Cancel
                    </a>
                    <button type="submit"
                        class="px-5 py-2 text-sm font-medium rounded-lg bg-yellow-500 hover:bg-yellow-600 text-white transition-colors">
                        Create & Assign Supervisor
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
