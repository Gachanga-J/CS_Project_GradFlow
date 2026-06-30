<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Notifications') }}
            </h2>
            @if(auth()->user()->unreadNotifications->count() > 0)
                <form method="POST" action="{{ route('notifications.read-all') }}">
                    @csrf
                    <button type="submit"
                            class="text-sm text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-200 font-medium">
                        Mark all as read
                    </button>
                </form>
            @endif
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">

                @forelse($notifications as $notification)
                    @php
                        $isDeadlineReminder = $notification->type === \App\Notifications\MilestoneDeadlineReminder::class;
                        $isSubmissionReview = $notification->type === \App\Notifications\SubmissionReviewed::class;
                        $isNewSubmission    = $notification->type === \App\Notifications\NewSubmissionReceived::class;
                        $isOverdue  = $isDeadlineReminder && ($notification->data['type'] ?? '') === 'overdue';
                        $isApproved = $isSubmissionReview && ($notification->data['decision'] ?? '') === 'approved';
                        $daysLeft  = $notification->data['days_left'] ?? null;
                        $isUnread  = is_null($notification->read_at);

                        $accentRed = $isOverdue || ($isSubmissionReview && ! $isApproved);
                    @endphp

                    <div class="flex items-start gap-4 px-6 py-4 border-b border-gray-100 dark:border-gray-700 last:border-b-0
                        {{ $isUnread ? ($accentRed ? 'bg-red-50 dark:bg-red-900/20' : 'bg-indigo-50 dark:bg-indigo-900/20') : '' }}">

                        {{-- Icon --}}
                        <div class="shrink-0 mt-0.5 text-xl">
                            @if($isDeadlineReminder)
                                {{ $isOverdue ? '🚨' : '⏰' }}
                            @elseif($isSubmissionReview)
                                {{ $isApproved ? '✅' : '❌' }}
                            @elseif($isNewSubmission)
                                📄
                            @else
                                🔔
                            @endif
                        </div>

                        {{-- Content --}}
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium {{ $isUnread ? ($accentRed ? 'text-red-700 dark:text-red-400' : 'text-gray-900 dark:text-gray-100') : 'text-gray-500 dark:text-gray-400' }}">
                                {{ $notification->data['milestone_title'] ?? 'Notification' }}
                            </p>

                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                                @if($isDeadlineReminder)
                                    @if($isOverdue)
                                        Deadline was {{ $notification->data['deadline'] }} — overdue!
                                    @else
                                        Deadline: {{ $notification->data['deadline'] }}
                                        @if(!is_null($daysLeft))
                                            · {{ $daysLeft === 0 ? 'due today' : "{$daysLeft} day" . ($daysLeft > 1 ? 's' : '') . ' left' }}
                                        @endif
                                    @endif
                                @elseif($isSubmissionReview || $isNewSubmission)
                                    {{ $notification->data['message'] ?? '' }}
                                @endif
                            </p>

                            <div class="flex items-center gap-4 mt-1.5">
                                <span class="text-xs text-gray-400 dark:text-gray-500">
                                    {{ $notification->created_at->diffForHumans() }}
                                </span>
                                @if(isset($notification->data['route']))
                                    <a href="{{ $notification->data['route'] }}"
                                       class="text-xs font-medium {{ $accentRed ? 'text-red-600 hover:text-red-800' : 'text-indigo-600 hover:text-indigo-800' }}">
                                        View →
                                    </a>
                                @endif
                            </div>
                        </div>

                        {{-- Mark as read --}}
                        @if($isUnread)
                            <form method="POST" action="{{ route('notifications.read', $notification->id) }}" class="shrink-0">
                                @csrf
                                <button type="submit"
                                        title="Mark as read"
                                        class="text-xs text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 mt-1">
                                    ✓
                                </button>
                            </form>
                        @endif
                    </div>

                @empty
                    <div class="px-6 py-16 text-center">
                        <p class="text-gray-400 dark:text-gray-500 text-sm">No notifications yet.</p>
                    </div>
                @endforelse
            </div>

            {{-- Pagination --}}
            @if($notifications->hasPages())
                <div class="mt-4">
                    {{ $notifications->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
