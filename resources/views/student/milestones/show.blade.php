<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $milestone->title }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('success'))
                <div class="p-4 bg-green-100 text-green-700 rounded border border-green-300">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('warning'))
                <div class="p-4 bg-orange-100 text-orange-700 rounded border border-orange-300">
                    ⚠️ {{ session('warning') }}
                </div>
            @endif

            {{-- Milestone Details --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-800">
                            {{ $milestone->sequence_order }}. {{ $milestone->title }}
                        </h3>
                        <span style="padding:3px 10px; border-radius:999px; font-size:11px; font-weight:600;
                            {{ $milestone->status === 'open' ? 'background:#dcfce7; color:#15803d;' : 'background:#f3f4f6; color:#6b7280;' }}">
                            {{ ucfirst($milestone->status) }}
                        </span>
                    </div>

                    @if ($milestone->description)
                        <p class="text-sm text-gray-600 mb-4">{{ $milestone->description }}</p>
                    @endif

                    @if ($milestone->deadline)
                        <p class="text-xs text-gray-400 mb-3">
                            Deadline: {{ $milestone->deadline->format('d M Y') }}
                        </p>

                        @php
                            $deadlineTs  = $milestone->deadline->endOfDay()->timestamp;
                            $nowTs       = now()->timestamp;
                            $secondsLeft = $deadlineTs - $nowTs;
                        @endphp

                        @if($milestone->status === 'open' && $secondsLeft > 0 && $secondsLeft <= 86400)
                            <div id="countdown-wrapper"
                                 class="flex items-center gap-3 mt-3 p-3 bg-red-50 border border-red-200 rounded-lg">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="shrink-0">
                                    <circle cx="12" cy="12" r="10"/>
                                    <polyline points="12 6 12 12 16 14"/>
                                </svg>
                                <p class="text-xs font-semibold text-red-700">
                                    Due in: <span id="countdown" class="font-mono text-sm"></span>
                                </p>
                            </div>
                        @elseif($milestone->status === 'open' && $secondsLeft <= 0)
                            <div class="flex items-center gap-3 mt-3 p-3 bg-red-50 border border-red-200 rounded-lg">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="shrink-0">
                                    <circle cx="12" cy="12" r="10"/>
                                    <line x1="12" y1="8" x2="12" y2="12"/>
                                    <line x1="12" y1="16" x2="12.01" y2="16"/>
                                </svg>
                                <p class="text-xs font-semibold text-red-700">This milestone is overdue!</p>
                            </div>
                        @endif
                    @endif
                </div>
            </div>

            {{-- Current Submission --}}
            @if ($submission)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Your Submission</h3>

                        <div class="p-4 border rounded-lg border-gray-200 bg-gray-50 mb-4">
                            <div class="flex items-center gap-2">
                                <p class="text-sm font-medium text-gray-700">{{ $submission->file_name }}</p>
                                @if($submission->submitted_late)
                                    <span style="padding:2px 8px; border-radius:999px; font-size:10px; font-weight:700; background:#fef3c7; color:#b45309; border:1px solid #fcd34d;">
                                        ⚠ Late
                                    </span>
                                @endif
                            </div>
                            <p class="text-xs text-gray-400 mt-1">
                                Version {{ $submission->version_number }} —
                                Submitted {{ $submission->submitted_at->format('d M Y, h:i A') }}
                            </p>
                        </div>

                        {{-- Status --}}
                        <div class="mb-4">
                            <span style="padding:4px 12px; border-radius:999px; font-size:11px; font-weight:600;
                                {{ $submission->status === 'graded' ? 'background:#dcfce7; color:#15803d;' : ($submission->status === 'supervisor_approved' ? 'background:#dbeafe; color:#1d4ed8;' : ($submission->status === 'supervisor_rejected' ? 'background:#fee2e2; color:#dc2626;' : 'background:#fef9c3; color:#a16207;')) }}">
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

            {{-- Late submission warning before upload form --}}
            @if($isLate && $milestone->status === 'open' && ($submission === null || $submission->status !== 'graded'))
                <div class="flex items-start gap-3 p-4 bg-orange-50 border border-orange-300 rounded-lg">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#ea580c" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mt-0.5 shrink-0">
                        <path d="M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
                        <line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/>
                    </svg>
                    <div>
                        <p class="text-sm font-semibold text-orange-700">You are submitting after the deadline</p>
                        <p class="text-xs text-orange-600 mt-0.5">
                            The deadline was {{ $milestone->deadline->format('d M Y') }}.
                            Your submission will be marked as <strong>late</strong> and the coordinator will be notified.
                        </p>
                    </div>
                </div>
            @endif

            {{-- Upload Form --}}
            @if ($milestone->status === 'open' && ($submission === null || $submission->status !== 'graded'))
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-2">
                            {{ $submission ? 'Resubmit Deliverable' : 'Submit Deliverable' }}
                        </h3>
                        <p class="text-sm text-gray-500 mb-6">
                            Accepted formats: <strong>PDF, DOC, DOCX</strong>. Max size: <strong>5MB</strong>.
                        </p>

                        <form method="POST"
                              action="{{ route('student.milestones.submit', $milestone) }}"
                              enctype="multipart/form-data">
                            @csrf

                            <div class="mb-6">
                                <div id="dropzone"
                                     class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md cursor-pointer transition-colors @error('file') border-red-500 @enderror">
                                    <div class="space-y-1 text-center">
                                        <svg id="dropzone-icon" class="mx-auto h-12 w-12 text-gray-400 transition-colors" stroke="currentColor" fill="none" viewBox="0 0 48 48">
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
                                   style="font-size:14px; font-weight:600; padding:8px 20px; border-radius:6px; border:2px solid #6b7280; background:white; color:#374151; text-decoration:none;">
                                    Cancel
                                </a>
                                <button type="submit"
                                        style="font-size:14px; font-weight:600; padding:8px 20px; border-radius:6px;
                                        {{ $isLate ? 'border:2px solid #d97706; background:#f59e0b; color:white;' : 'border:2px solid #3730a3; background:#4f46e5; color:white;' }} cursor:pointer;">
                                    {{ $isLate ? '⚠ Submit Late' : ($submission ? 'Resubmit Milestone' : 'Submit Milestone') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            @elseif ($submission?->status === 'graded')
                <div class="p-4 bg-green-50 border border-green-200 text-green-700 rounded-lg text-sm text-center">
                    This submission has been graded. No further submissions are allowed.
                </div>
            @else
                <div class="p-4 bg-gray-100 border border-gray-200 text-gray-500 rounded-lg text-sm text-center">
                    This milestone is closed and no longer accepting submissions.
                </div>
            @endif

            {{-- Version History --}}
            @if($submissionHistory->count() > 1)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <button onclick="document.getElementById('version-history').classList.toggle('hidden')"
                                class="flex items-center justify-between w-full text-left">
                            <h3 class="text-sm font-semibold text-gray-700">
                                Submission History
                                <span class="ml-2 text-xs font-medium px-2 py-0.5 rounded-full bg-gray-100 text-gray-500">
                                    {{ $submissionHistory->count() }} version{{ $submissionHistory->count() > 1 ? 's' : '' }}
                                </span>
                            </h3>
                            <svg id="history-chevron" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#6b7280" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="6 9 12 15 18 9"/>
                            </svg>
                        </button>

                        <div id="version-history" class="hidden mt-4 space-y-2">
                            @foreach($submissionHistory as $version)
                                <div class="flex items-center justify-between p-3 rounded-lg border
                                    {{ $version->is_latest ? 'border-indigo-200 bg-indigo-50' : 'border-gray-100 bg-gray-50' }}">
                                    <div class="flex items-center gap-3">
                                        <span class="text-xs font-bold px-2 py-1 rounded
                                            {{ $version->is_latest ? 'bg-indigo-100 text-indigo-700' : 'bg-gray-200 text-gray-500' }}">
                                            v{{ $version->version_number }}
                                        </span>
                                        <div>
                                            <div class="flex items-center gap-2">
                                                <p class="text-sm font-medium text-gray-700">{{ $version->file_name }}</p>
                                                @if($version->submitted_late)
                                                    <span style="padding:1px 6px; border-radius:999px; font-size:10px; font-weight:700; background:#fef3c7; color:#b45309; border:1px solid #fcd34d;">
                                                        ⚠ Late
                                                    </span>
                                                @endif
                                            </div>
                                            <p class="text-xs text-gray-400 mt-0.5">
                                                {{ $version->submitted_at->format('d M Y, h:i A') }}
                                                @if($version->file_size_bytes)
                                                    · {{ number_format($version->file_size_bytes / 1024, 1) }} KB
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        @if($version->is_latest)
                                            <span class="text-xs font-medium text-indigo-600">Latest</span>
                                        @endif
                                        <span style="padding:2px 8px; border-radius:999px; font-size:11px; font-weight:600;
                                            {{ $version->status === 'graded' ? 'background:#dcfce7; color:#15803d;' : ($version->status === 'supervisor_approved' ? 'background:#dbeafe; color:#1d4ed8;' : ($version->status === 'supervisor_rejected' ? 'background:#fee2e2; color:#dc2626;' : 'background:#fef9c3; color:#a16207;')) }}">
                                            {{ ucfirst(str_replace('_', ' ', $version->status)) }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
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
        // ── Countdown Timer ──────────────────────────────────────────
        const countdownEl = document.getElementById('countdown');
        if (countdownEl) {
            const deadlineTs = {{ $milestone->deadline?->endOfDay()->timestamp ?? 0 }};

            function updateCountdown() {
                const secondsLeft = deadlineTs - Math.floor(Date.now() / 1000);
                if (secondsLeft <= 0) {
                    countdownEl.textContent = 'Deadline passed!';
                    document.getElementById('countdown-wrapper').classList.add('animate-pulse');
                    clearInterval(timer);
                    return;
                }
                const h = Math.floor(secondsLeft / 3600);
                const m = Math.floor((secondsLeft % 3600) / 60);
                const s = secondsLeft % 60;
                countdownEl.textContent =
                    String(h).padStart(2, '0') + ':' +
                    String(m).padStart(2, '0') + ':' +
                    String(s).padStart(2, '0');
            }

            updateCountdown();
            const timer = setInterval(updateCountdown, 1000);
        }

        // ── Version History Toggle ───────────────────────────────────
        const historyBtn = document.querySelector('[onclick*="version-history"]');
        if (historyBtn) {
            historyBtn.addEventListener('click', () => {
                const chevron  = document.getElementById('history-chevron');
                const isHidden = document.getElementById('version-history').classList.contains('hidden');
                chevron.style.transform  = isHidden ? 'rotate(180deg)' : 'rotate(0deg)';
                chevron.style.transition = 'transform 0.2s ease';
            });
        }

        // ── Drag and Drop ────────────────────────────────────────────
        const dropzone   = document.getElementById('dropzone');
        const fileInput  = document.getElementById('file');
        const fileChosen = document.getElementById('file-chosen');
        const icon       = document.getElementById('dropzone-icon');

        if (dropzone) {
            dropzone.addEventListener('click', () => fileInput.click());

            fileInput.addEventListener('change', function () {
                if (this.files[0]) showFile(this.files[0]);
            });

            dropzone.addEventListener('dragover', (e) => {
                e.preventDefault();
                dropzone.classList.add('border-indigo-400', 'bg-indigo-50');
                icon.classList.add('text-indigo-400');
                icon.classList.remove('text-gray-400');
            });

            dropzone.addEventListener('dragleave', (e) => {
                e.preventDefault();
                dropzone.classList.remove('border-indigo-400', 'bg-indigo-50');
                icon.classList.remove('text-indigo-400');
                icon.classList.add('text-gray-400');
            });

            dropzone.addEventListener('drop', (e) => {
                e.preventDefault();
                dropzone.classList.remove('border-indigo-400', 'bg-indigo-50');
                icon.classList.remove('text-indigo-400');
                icon.classList.add('text-gray-400');

                const file = e.dataTransfer.files[0];
                if (!file) return;

                const allowed = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
                if (!allowed.includes(file.type)) {
                    fileChosen.textContent = '❌ Invalid file type. Please use PDF, DOC, or DOCX.';
                    fileChosen.style.color = '#dc2626';
                    return;
                }

                if (file.size > 5 * 1024 * 1024) {
                    fileChosen.textContent = '❌ File too large. Maximum size is 5MB.';
                    fileChosen.style.color = '#dc2626';
                    return;
                }

                const dt = new DataTransfer();
                dt.items.add(file);
                fileInput.files = dt.files;
                showFile(file);
            });

            function showFile(file) {
                fileChosen.textContent = '✓ ' + file.name;
                fileChosen.style.color = '#16a34a';
                dropzone.classList.add('border-green-400', 'bg-green-50');
                dropzone.classList.remove('border-gray-300');
                icon.classList.add('text-green-400');
                icon.classList.remove('text-gray-400');
            }
        }
    </script>

</x-app-layout>
