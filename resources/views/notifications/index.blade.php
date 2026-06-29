<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Notifications') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">

                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                    <p class="text-sm font-semibold text-gray-800 dark:text-gray-100">
                        All Notifications
                    </p>
                    @if (Auth::user()->unreadNotifications->count() > 0)
                        <form method="POST" action="{{ route('notifications.read-all') }}">
                            @csrf
                            <button type="submit" class="text-xs text-indigo-600 hover:text-indigo-800 font-medium">
                                Mark all read
                            </button>
                        </form>
                    @endif
                </div>

                <div class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse ($notifications as $notification)
                        @php
                            $isOverdue = ($notification->data['type'] ?? '') === 'overdue';
                            $daysLeft  = $notification->data['days_left'] ?? null;
                        @endphp
                        <div class="px-6 py-4 {{ $notification->read_at ? '' : ($isOverdue ? 'bg-red-50 dark:bg-red-900/20' : 'bg-indigo-50 dark:bg-indigo-900/20') }}">
                            <div class="flex items-start justify-between gap-4">
                                <div class="flex-1">
                                    <p class="text-sm font-medium {{ $isOverdue ? 'text-red-700' : 'text-gray-800 dark:text-gray-100' }}">
                                        {{ $isOverdue ? '🚨' : '⏰' }} {{ $notification->data['milestone_title'] ?? 'Notification' }}
                                    </p>
                                    <p class="text-xs text-gray-500 mt-0.5">
                                        @if($isOverdue)
                                            Deadline was {{ $notification->data['deadline'] ?? '—' }} — overdue!
                                        @else
                                            Deadline: {{ $notification->data['deadline'] ?? '—' }}
                                            @if($daysLeft !== null)
                                                · {{ $daysLeft === 0 ? 'due today' : "{$daysLeft} day" . ($daysLeft > 1 ? 's' : '') . ' left' }}
                                            @endif
                                        @endif
                                    </p>
                                    <div class="flex items-center gap-3 mt-1">
                                        <p class="text-xs text-gray-400">
                                            {{ $notification->created_at->diffForHumans() }}
                                        </p>
                                        @if (isset($notification->data['route']))
                                            <a href="{{ $notification->data['route'] }}"
                                               class="text-xs font-medium {{ $isOverdue ? 'text-red-600 hover:text-red-800' : 'text-indigo-600 hover:text-indigo-800' }}">
                                                View milestone →
                                            </a>
                                        @endif
                                    </div>
                                </div>

                                @if (! $notification->read_at)
                                    <form method="POST" action="{{ route('notifications.read', $notification->id) }}">
                                        @csrf
                                        <button type="submit" class="text-xs text-gray-400 hover:text-gray-600 whitespace-nowrap">
                                            ✓ Mark read
                                        </button>
                                    </form>
                                @else
                                    <span class="text-xs text-gray-300 whitespace-nowrap">Read</span>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="px-6 py-10 text-center text-sm text-gray-400">
                            No notifications yet.
                        </div>
                    @endforelse
                </div>

                @if ($notifications->hasPages())
                    <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700">
                        {{ $notifications->links() }}
                    </div>
                @endif

            </div>

        </div>
    </div>
</x-app-layout>
