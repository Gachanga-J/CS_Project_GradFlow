<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Student Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <p>{{ __('Welcome back, :name!', ['name' => auth()->user()->full_name]) }}</p>

                    <ul class="mt-4 space-y-1 text-sm text-gray-600 dark:text-gray-400">
                        <li><strong>{{ __('Email') }}:</strong> {{ auth()->user()->email }}</li>
                        <li><strong>{{ __('Registration Number') }}:</strong> {{ auth()->user()->student->reg_number }}</li>
                        <li><strong>{{ __('Year of Study') }}:</strong> {{ auth()->user()->student->year_of_study ?? __('Not set') }}</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>