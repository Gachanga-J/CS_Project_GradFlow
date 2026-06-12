<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Supervisor Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <p>{{ __('Welcome back, :name!', ['name' => auth()->user()->full_name]) }}</p>

                    <ul class="mt-4 space-y-1 text-sm text-gray-600 dark:text-gray-400">
                        <li><strong>{{ __('Email') }}:</strong> {{ auth()->user()->email }}</li>
                        <li><strong>{{ __('Staff Number') }}:</strong> {{ auth()->user()->supervisor->staff_number ?? __('Not set') }}</li>
                        <li><strong>{{ __('Max Student Capacity') }}:</strong> {{ auth()->user()->supervisor->max_student_capacity }}</li>
                        <li><strong>{{ __('Current Load') }}:</strong> {{ auth()->user()->supervisor->current_load }}</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>