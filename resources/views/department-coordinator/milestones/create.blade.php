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

                    <h3 class="text-lg font-semibold text-gray-700 mb-6">
                        New Milestone / Deliverable
                    </h3>

                    <form method="POST" action="{{ route('department-coordinator.milestones.store') }}">
                        @csrf

                        {{-- Title --}}
                        <div class="mb-4">
                            <label for="title"
                                   class="block text-sm font-medium text-gray-700 mb-1">
                                Title <span class="text-red-500">*</span>
                            </label>
                            <input type="text"
                                   id="title"
                                   name="title"
                                   value="{{ old('title') }}"
                                   placeholder="e.g. Project Proposal, Literature Review"
                                   class="w-full border-gray-300 rounded-md shadow-sm focus:ring focus:ring-indigo-200 @error('title') border-red-500 @enderror">
                            @error('title')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Description --}}
                        <div class="mb-4">
                            <label for="description"
                                   class="block text-sm font-medium text-gray-700 mb-1">
                                Description <span class="text-gray-400 text-xs">(optional)</span>
                            </label>
                            <textarea id="description"
                                      name="description"
                                      rows="4"
                                      placeholder="Instructions or details for students..."
                                      class="w-full border-gray-300 rounded-md shadow-sm focus:ring focus:ring-indigo-200 @error('description') border-red-500 @enderror">{{ old('description') }}</textarea>
                            @error('description')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Deadline --}}
                        <div class="mb-4">
                            <label for="deadline"
                                   class="block text-sm font-medium text-gray-700 mb-1">
                                Deadline <span class="text-gray-400 text-xs">(optional)</span>
                            </label>
                            <input type="date"
                                   id="deadline"
                                   name="deadline"
                                   value="{{ old('deadline') }}"
                                   class="w-full border-gray-300 rounded-md shadow-sm focus:ring focus:ring-indigo-200 @error('deadline') border-red-500 @enderror">
                            @error('deadline')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Sequence Order --}}
                        <div class="mb-6">
                            <label for="sequence_order"
                                   class="block text-sm font-medium text-gray-700 mb-1">
                                Sequence Order <span class="text-red-500">*</span>
                            </label>
                            <input type="number"
                                   id="sequence_order"
                                   name="sequence_order"
                                   value="{{ old('sequence_order', 1) }}"
                                   min="1"
                                   class="w-32 border-gray-300 rounded-md shadow-sm focus:ring focus:ring-indigo-200 @error('sequence_order') border-red-500 @enderror">
                            <p class="text-xs text-gray-400 mt-1">
                                Controls the order milestones appear to students (1 = first).
                            </p>
                            @error('sequence_order')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Buttons --}}
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