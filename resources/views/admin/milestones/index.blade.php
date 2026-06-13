<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Milestones') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('success'))
                <div class="p-4 bg-green-100 text-green-700 rounded">
                    ✅ {{ session('success') }}
                </div>
            @endif

            <div class="flex justify-between items-center">
                <h3 class="text-lg font-semibold text-gray-800">🏁 All Milestones</h3>
                <a href="{{ route('admin.milestones.create') }}"
                   class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold py-2 px-5 rounded-md">
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
                                                📅 Deadline: {{ $milestone->deadline->format('d M Y') }}
                                            </p>
                                        @endif
                                    </div>

                                    <div class="flex items-center gap-3 shrink-0 ml-4">
                                        <span class="px-3 py-1 rounded-full text-xs font-medium
                                            @if($milestone->status === 'open') bg-indigo-100 text-indigo-700
                                            @else bg-gray-200 text-gray-600
                                            @endif">
                                            {{ ucfirst($milestone->status) }}
                                        </span>

                                        {{-- Toggle Status --}}
                                        <form method="POST"
                                              action="{{ route('admin.milestones.toggle', $milestone) }}">
                                            @csrf
                                            <button type="submit"
                                                    class="text-xs font-medium py-1 px-3 rounded-md
                                                    @if($milestone->status === 'open') bg-yellow-100 hover:bg-yellow-200 text-yellow-700
                                                    @else bg-green-100 hover:bg-green-200 text-green-700
                                                    @endif">
                                                {{ $milestone->status === 'open' ? 'Close' : 'Reopen' }}
                                            </button>
                                        </form>

                                        {{-- Delete --}}
                                        <form method="POST"
                                              action="{{ route('admin.milestones.destroy', $milestone) }}"
                                              onsubmit="return confirm('Delete this milestone?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="text-xs font-medium py-1 px-3 rounded-md bg-red-100 hover:bg-red-200 text-red-700">
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