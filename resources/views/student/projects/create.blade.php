<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Submit Project Proposal') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">

                    @if (session('info'))
                        <div class="mb-4 p-4 bg-blue-100 text-blue-700 rounded">
                            {{ session('info') }}
                        </div>
                    @endif

                    <h3 class="text-lg font-semibold text-gray-700 mb-2">
                        📄 New Project Proposal
                    </h3>
                    <p class="text-sm text-gray-500 mb-6">
                        Upload your proposal document. The admin will review it and get back to you.
                        Accepted formats: <strong>PDF, DOC, DOCX</strong>. Max size: <strong>5MB</strong>.
                    </p>

                    <form method="POST"
                          action="{{ route('student.projects.store') }}"
                          enctype="multipart/form-data">
                        @csrf

                        {{-- Project Title --}}
                        <div class="mb-4">
                            <label for="title"
                                   class="block text-sm font-medium text-gray-700 mb-1">
                                Project Title <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="text"
                                id="title"
                                name="title"
                                value="{{ old('title') }}"
                                class="w-full border-gray-300 rounded-md shadow-sm focus:ring focus:ring-indigo-200 @error('title') border-red-500 @enderror"
                                placeholder="Enter your project title"
                            >
                            @error('title')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Proposal File Upload --}}
                        <div class="mb-6">
                            <label for="proposal"
                                   class="block text-sm font-medium text-gray-700 mb-1">
                                Proposal Document <span class="text-red-500">*</span>
                            </label>
                            <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md @error('proposal') border-red-500 @enderror">
                                <div class="space-y-1 text-center">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                    <div class="text-sm text-gray-600">
                                        <label for="proposal" class="cursor-pointer font-medium text-indigo-600 hover:text-indigo-500">
                                            Click to upload
                                        </label>
                                        <span> or drag and drop</span>
                                    </div>
                                    <p class="text-xs text-gray-500">PDF, DOC, DOCX up to 5MB</p>
                                    <input id="proposal" name="proposal" type="file"
                                           accept=".pdf,.doc,.docx" class="sr-only">
                                </div>
                            </div>
                            {{-- Show selected file name --}}
                            <p id="file-chosen" class="text-sm text-gray-500 mt-2"></p>
                            @error('proposal')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Buttons --}}
                        <div class="flex items-center justify-end">
                            <a href="{{ route('dashboard.student') }}"
                               class="text-sm text-gray-500 hover:text-gray-700 mr-4">
                                Cancel
                            </a>
                            <button type="submit"
                                    class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-6 rounded-md">
                                Submit Proposal
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Show selected filename when user picks a file --}}
    <script>
        document.getElementById('proposal').addEventListener('change', function () {
            const fileName = this.files[0] ? this.files[0].name : 'No file chosen';
            document.getElementById('file-chosen').textContent = '📎 ' + fileName;
        });
    </script>

</x-app-layout>