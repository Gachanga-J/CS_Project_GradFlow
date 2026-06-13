<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $milestone->title }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('success'))
                <div class="p-4 bg-green-100 text-green-700 rounded">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Milestone Details --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-800">
                            {{ $milestone->sequence_order }}. {{ $milestone->title }}
                        </h3>
                        <span class="px-3 py-1 rounded-full text-xs font-medium
                            @if($milestone->status === 'open') bg-green-100 text-green-700
                            @else bg-gray-100 text-gray-600
                            @endif">
                            {{ ucfirst($milestone->status) }}
                        </span>
                    </div>

                    @if ($milestone->description)
                        <p class="text-sm text-gray-600 mb-4">{{ $milestone->description }}</p>
                    @endif

                    @if ($milestone->deadline)
                        <p class="text-xs text-gray-400">
                            Deadline: {{ $milestone->deadline->format('d M Y') }}
                        </p>
                    @endif
                </div>
            </div>

            {{-- Current Submission --}}
            @if ($submission)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Your Submission</h3>

                        <div class="p-4 border rounded-lg border-gray-200 bg-gray-50 mb-4">
                            <p class="text-sm font-medium text-gray-700">{{ $submission->file_name }}</p>
                            <p class="text-xs text-gray-400 mt-1">
                                Version {{ $submission->version_number }} —
                                Submitted {{ $submission->submitted_at->format('d M Y, h:i A') }}
                            </p>
                        </div>

                        {{-- Status --}}
                        <div class="mb-4">
                            <span class="px-3 py-1 rounded-full text-xs font-medium
                                @if($submission->status === 'graded') bg-green-100 text-green-700
                                @elseif($submission->status === 'supervisor_approved') bg-blue-100 text-blue-700
                                @elseif($submission->status === 'supervisor_rejected') bg-red-100 text-red-700
                                @else bg-yellow-100 text-yellow-700
                                @endif">
                                {{ ucfirst(str_replace('_', ' ', $submission->status)) }}
                            </span>
                        </div>

                        {{-- Supervisor Feedback --}}
                        @if ($submission->supervisor_feedback)
                            <div class="p-3 bg-blue-50 border border-blue-200 rounded-lg mb-3">
                                <p class="text-xs font-semibold text-blue-700 mb-1">Supervisor Feedback:</p>
                                <p class="text-sm text-gray-600">{{ $submission->supervisor_feedback }}</p>
                            </div>
                        @endif

                        {{-- Admin Feedback & Grade --}}
                        @if ($submission->admin_feedback || $submission->grade !== null)
                            <div class="p-3 bg-green-50 border border-green-200 rounded-lg">
                                @if ($submission->grade !== null)
                                    <p class="text-xs font-semibold text-green-700 mb-1">
                                        Grade: <span class="text-2xl font-bold">{{ $submission->grade }}</span>/100
                                    </p>
                                @endif
                                @if ($submission->admin_feedback)
                                    <p class="text-xs font-semibold text-green-700 mb-1">Admin Feedback:</p>
                                    <p class="text-sm text-gray-600">{{ $submission->admin_feedback }}</p>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            {{-- Upload Form --}}
            @if ($milestone->status === 'open')
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-2">
                            {{ $submission ? 'Resubmit' : 'Submit Deliverable' }}
                        </h3>
                        <p class="text-sm text-gray-500 mb-6">
                            Accepted formats: <strong>PDF, DOC, DOCX</strong>. Max size: <strong>5MB</strong>.
                        </p>

                        <form method="POST"
                              action="{{ route('student.milestones.submit', $milestone) }}"
                              enctype="multipart/form-data">
                            @csrf

                            <div class="mb-6">
                                <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md @error('file') border-red-500 @enderror">
                                    <div class="space-y-1 text-center">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                        <div class="text-sm text-gray-600">
                                            <label for="file" class="cursor-pointer font-medium text-indigo-600 hover:text-indigo-500">
                                                Click to upload
                                            </label>
                                            <span> or drag and drop</span>
                                        </div>
                                        <p class="text-xs text-gray-500">PDF, DOC, DOCX up to 5MB</p>
                                        <input id="file" name="file" type="file"
                                               accept=".pdf,.doc,.docx" class="sr-only">
                                    </div>
                                </div>
                                <p id="file-chosen" class="text-sm text-gray-500 mt-2"></p>
                                @error('file')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="flex items-center justify-end gap-4">
                                <a href="{{ route('dashboard.student') }}"
                                   class="text-sm text-gray-500 hover:text-gray-700">
                                    Cancel
                                </a>
                                <button type="submit"
                                        class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-6 rounded-md">
                                    {{ $submission ? 'Resubmit' : 'Submit' }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            @else
                <div class="p-4 bg-gray-100 text-gray-500 rounded-lg text-sm text-center">
                    This milestone is closed and no longer accepting submissions.
                </div>
            @endif

            <div>
                <a href="{{ route('dashboard.student') }}"
                   class="text-sm text-indigo-600 hover:text-indigo-800">
                    Back to Dashboard
                </a>
            </div>

        </div>
    </div>

    <script>
        document.getElementById('file').addEventListener('change', function () {
            const fileName = this.files[0] ? this.files[0].name : 'No file chosen';
            document.getElementById('file-chosen').textContent = fileName;
        });
    </script>

</x-app-layout>