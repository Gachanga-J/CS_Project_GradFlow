<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Milestones') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('success'))
                <div class="p-4 bg-green-100 text-green-700 rounded border border-green-300">
                    {{ session('success') }}
                </div>
            @endif

            <div style="display:flex; justify-content:space-between; align-items:center;">
                <h3 class="text-lg font-semibold text-gray-800">All Milestones</h3>
                <a href="{{ route('department-coordinator.milestones.create') }}"
                   style="display:inline-flex; align-items:center; background:#4f46e5; color:white; font-weight:600; font-size:14px; padding:10px 20px; border-radius:6px; border:2px solid #3730a3; text-decoration:none;">
                    + New Milestone
                </a>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">

                    @if ($milestones->isEmpty())
                        <p class="text-sm text-gray-400 italic">No milestones created yet.</p>
                    @else
                        <div class="space-y-3">
                            @foreach ($milestones as $milestone)
                                <div class="flex items-start justify-between p-4 border rounded-lg
                                    @if($milestone->status === 'open') border-indigo-200 bg-indigo-50
                                    @else border-gray-200 bg-gray-50
                                    @endif">

                                    <div>
                                        <p class="font-semibold text-gray-800 text-sm">
                                            {{ $milestone->sequence_order }}. {{ $milestone->title }}
                                        </p>
                                        @if ($milestone->description)
                                            <p class="text-xs text-gray-500 mt-1">{{ $milestone->description }}</p>
                                        @endif
                                        @if ($milestone->deadline)
                                            <p class="text-xs text-gray-400 mt-1">
                                                Deadline: {{ $milestone->deadline->format('d M Y') }}
                                            </p>
                                        @endif
                                    </div>

                                    <div style="display:flex; align-items:center; gap:8px; flex-shrink:0; margin-left:16px;">

                                        <span style="padding:3px 10px; border-radius:999px; font-size:11px; font-weight:600;
                                            {{ $milestone->status === 'open' ? 'background:#e0e7ff; color:#3730a3;' : 'background:#f3f4f6; color:#6b7280;' }}">
                                            {{ ucfirst($milestone->status) }}
                                        </span>

                                        {{-- Edit --}}
                                        <a href="{{ route('department-coordinator.milestones.edit', $milestone) }}"
                                           style="font-size:13px; font-weight:600; padding:6px 14px; border-radius:6px; border:2px solid #6b7280; background:white; color:#374151; text-decoration:none;">
                                            Edit
                                        </a>

                                        {{-- Toggle Status --}}
                                        <form method="POST" action="{{ route('department-coordinator.milestones.toggle', $milestone) }}">
                                            @csrf
                                            <button type="submit"
                                                    style="font-size:13px; font-weight:600; padding:6px 14px; border-radius:6px; cursor:pointer;
                                                    {{ $milestone->status === 'open'
                                                        ? 'border:2px solid #d97706; background:white; color:#d97706;'
                                                        : 'border:2px solid #16a34a; background:white; color:#16a34a;' }}">
                                                {{ $milestone->status === 'open' ? 'Close' : 'Reopen' }}
                                            </button>
                                        </form>

                                        {{-- Delete --}}
                                        <form method="POST"
                                              action="{{ route('department-coordinator.milestones.destroy', $milestone) }}"
                                              onsubmit="return confirm('Delete this milestone?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    style="font-size:13px; font-weight:600; padding:6px 14px; border-radius:6px; border:2px solid #dc2626; background:white; color:#dc2626; cursor:pointer;">
                                                Delete
                                            </button>
                                        </form>

                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif

                </div>
            </div>

        </div>
    </div>
</x-app-layout>