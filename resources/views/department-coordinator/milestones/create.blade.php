<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Create Milestone') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-700 mb-6">New Milestone / Deliverable</h3>

                    <form method="POST" action="{{ route('department-coordinator.milestones.store') }}">
                        @csrf

                        {{-- Cohort (intake year + year of study) --}}
                        <div class="mb-4 grid grid-cols-2 gap-4">
                            <div>
                                <label for="intake_year" class="block text-sm font-medium text-gray-700 mb-1">
                                    Intake Year (Cohort) <span class="text-red-500">*</span>
                                </label>
                                @if(!empty($cohortOptions))
                                    <select id="intake_year" name="intake_year"
                                            class="w-full border-gray-300 rounded-md shadow-sm focus:ring focus:ring-indigo-200 @error('intake_year') border-red-500 @enderror"
                                            required>
                                        <option value="">-- Select --</option>
                                        @foreach(array_keys($cohortOptions) as $year)
                                            <option value="{{ $year }}" {{ old('intake_year') == $year ? 'selected' : '' }}>
                                                {{ $year }} Cohort
                                            </option>
                                        @endforeach
                                    </select>
                                @else
                                    <input type="number" id="intake_year" name="intake_year"
                                           value="{{ old('intake_year', now()->year) }}"
                                           min="2000" max="{{ now()->year }}"
                                           class="w-full border-gray-300 rounded-md shadow-sm focus:ring focus:ring-indigo-200"
                                           required>
                                    <p class="text-xs text-gray-400 mt-1">No students registered yet.</p>
                                @endif
                                @error('intake_year')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="year_of_study" class="block text-sm font-medium text-gray-700 mb-1">
                                    Year of Study <span class="text-red-500">*</span>
                                </label>
                                <select id="year_of_study" name="year_of_study"
                                        class="w-full border-gray-300 rounded-md shadow-sm focus:ring focus:ring-indigo-200 @error('year_of_study') border-red-500 @enderror"
                                        required>
                                    <option value="">-- Select --</option>
                                    @foreach([1,2,3,4,5,6] as $year)
                                        <option value="{{ $year }}" {{ old('year_of_study') == $year ? 'selected' : '' }}>
                                            Year {{ $year }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('year_of_study')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        {{-- Title --}}
                        <div class="mb-4">
                            <label for="title" class="block text-sm font-medium text-gray-700 mb-1">
                                Title <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="title" name="title" value="{{ old('title') }}"
                                   placeholder="e.g. Project Proposal, Literature Review"
                                   class="w-full border-gray-300 rounded-md shadow-sm focus:ring focus:ring-indigo-200 @error('title') border-red-500 @enderror">
                            @error('title')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Description --}}
                        <div class="mb-4">
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-1">
                                Description <span class="text-gray-400 text-xs">(optional)</span>
                            </label>
                            <textarea id="description" name="description" rows="4"
                                      placeholder="Instructions or details for students..."
                                      class="w-full border-gray-300 rounded-md shadow-sm focus:ring focus:ring-indigo-200">{{ old('description') }}</textarea>
                        </div>

                        {{-- Deadline --}}
                        <div class="mb-4">
                            <label for="deadline" class="block text-sm font-medium text-gray-700 mb-1">
                                Deadline <span class="text-gray-400 text-xs">(optional)</span>
                            </label>
                            <input type="date" id="deadline" name="deadline" value="{{ old('deadline') }}"
                                   class="w-full border-gray-300 rounded-md shadow-sm focus:ring focus:ring-indigo-200">
                        </div>

                        {{-- Sequence Order --}}
                        <div class="mb-6">
                            <label for="sequence_order" class="block text-sm font-medium text-gray-700 mb-1">
                                Sequence Order <span class="text-red-500">*</span>
                            </label>
                            <input type="number" id="sequence_order" name="sequence_order"
                                   value="{{ old('sequence_order', 1) }}" min="1"
                                   class="w-32 border-gray-300 rounded-md shadow-sm focus:ring focus:ring-indigo-200">
                            <p class="text-xs text-gray-400 mt-1">Controls the order milestones appear to students (1 = first).</p>
                        </div>

                        <div class="flex items-center justify-end gap-4">
                            <a href="{{ route('department-coordinator.milestones.index') }}"
                               style="font-size:14px; font-weight:600; padding:8px 20px; border-radius:6px; border:2px solid #6b7280; background:white; color:#374151; text-decoration:none;">
                                Cancel
                            </a>
                            <button type="submit"
                                    style="font-size:14px; font-weight:600; padding:8px 20px; border-radius:6px; border:2px solid #3730a3; background:#4f46e5; color:white; cursor:pointer;">
                                Create Milestone
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
