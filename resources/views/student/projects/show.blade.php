<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('My Project') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Flash Messages --}}
            @if (session('success'))
                <div class="p-4 bg-green-100 text-green-700 rounded">
                    ✅ {{ session('success') }}
                </div>
            @endif

            @if (session('info'))
                <div class="p-4 bg-blue-100 text-blue-700 rounded">
                    ℹ️ {{ session('info') }}
                </div>
            @endif

            {{-- Project Details Card --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">

                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-800">
                            📁 {{ $project->title }}
                        </h3>
                        <span class="px-3 py-1 rounded-full text-sm font-medium
                            @if($project->status === 'active') bg-green-100 text-green-700
                            @elseif($project->status === 'submitted') bg-yellow-100 text-yellow-700
                            @elseif($project->status === 'completed') bg-blue-100 text-blue-700
                            @elseif($project->status === 'rejected') bg-red-100 text-red-700
                            @elseif($project->status === 'cancelled') bg-gray-100 text-gray-500
                            @else bg-gray-100 text-gray-700
                            @endif">
                            {{ ucfirst($project->status) }}
                        </span>
                    </div>

                    {{-- Submitted At --}}
                    <p class="text-xs text-gray-400 mb-4">
                        Submitted: {{ $project->submitted_at?->format('d M Y, h:i A') ?? 'N/A' }}
                    </p>

                    {{-- Proposal File --}}
                    @if ($project->proposal_file_name)
                        <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg border border-gray-200">
                            <span class="text-2xl">
                                {{ str_ends_with($project->proposal_file_name, '.pdf') ? '📕' : '📘' }}
                            </span>
                            <div>
                                <p class="text-sm font-medium text-gray-700">
                                    {{ $project->proposal_file_name }}
                                </p>
                                <p class="text-xs text-gray-400">Proposal document</p>
                            </div>
                        </div>
                    @endif

                    {{-- Admin Comment --}}
                    @if ($project->admin_comment)
                        <div class="mt-4 p-4 rounded-lg
                            @if($project->status === 'rejected') bg-red-50 border border-red-200
                            @else bg-green-50 border border-green-200
                            @endif">
                            <p class="text-sm font-medium text-gray-700 mb-1">
                                💬 Admin Comment:
                            </p>
                            <p class="text-sm text-gray-600">
                                {{ $project->admin_comment }}
                            </p>
                        </div>
                    @endif

                    {{-- Status Messages --}}
                    @if ($project->status === 'submitted')
                        <div class="mt-4 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                            <p class="text-sm text-yellow-700">
                                ⏳ Your proposal is awaiting admin review. You will be notified once it is reviewed.
                            </p>
                        </div>
                    @elseif ($project->status === 'rejected')
                        <div class="mt-4 p-3 bg-red-50 border border-red-200 rounded-lg">
                            <p class="text-sm text-red-700">
                                ❌ Your proposal was rejected. Please review the admin comment above and resubmit.
                            </p>
                        </div>
                    @elseif ($project->status === 'active')
                        <div class="mt-4 p-3 bg-green-50 border border-green-200 rounded-lg">
                            <p class="text-sm text-green-700">
                                🎉 Your proposal was approved! You can now work on your milestones below.
                            </p>
                        </div>
                    @endif

                </div>
            </div>

            {{-- Milestones Card --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">
                        🏁 Milestones
                    </h3>

                    @if ($milestones->isEmpty())
                        <p class="text-gray-400 text-sm italic">
                            No milestones have been assigned yet.
                        </p>
                    @else
                        <div class="space-y-3">
                            @foreach ($milestones as $milestone)
                                <div class="flex items-center justify-between p-4 border rounded-lg
                                    @if($milestone->status === 'approved') border-green-200 bg-green-50
                                    @elseif($milestone->status === 'rejected') border-red-200 bg-red-50
                                    @elseif($milestone->status === 'open') border-indigo-200 bg-indigo-50
                                    @elseif($milestone->status === 'submitted' || $milestone->status === 'under_review') border-yellow-200 bg-yellow-50
                                    @else border-gray-200 bg-gray-50
                                    @endif">
                                    <div>
                                        <p class="font-medium text-gray-800 text-sm">
                                            {{ $milestone->sequence_order }}. {{ $milestone->title }}
                                        </p>
                                        @if ($milestone->deadline)
                                            <p class="text-xs text-gray-400 mt-1">
                                                Due: {{ $milestone->deadline->format('d M Y') }}
                                            </p>
                                        @endif
                                    </div>
                                    <span class="px-3 py-1 rounded-full text-xs font-medium
                                        @if($milestone->status === 'approved') bg-green-100 text-green-700
                                        @elseif($milestone->status === 'rejected') bg-red-100 text-red-700
                                        @elseif($milestone->status === 'open') bg-indigo-100 text-indigo-700
                                        @elseif($milestone->status === 'submitted') bg-yellow-100 text-yellow-700
                                        @elseif($milestone->status === 'under_review') bg-orange-100 text-orange-700
                                        @else bg-gray-100 text-gray-600
                                        @endif">
                                        {{ ucfirst(str_replace('_', ' ', $milestone->status)) }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            {{-- Back Button --}}
            <div>
                <a href="{{ route('dashboard.student') }}"
                   class="text-sm text-indigo-600 hover:text-indigo-800">
                    ← Back to Dashboard
                </a>
            </div>

        </div>
    </div>
</x-app-layout>