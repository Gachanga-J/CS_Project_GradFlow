<x-guest-layout>
    <div class="text-center mb-6">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
            {{ __('How would you like to register?') }}
        </h2>
    </div>

    <div class="flex flex-col gap-4">
        <a href="{{ route('register.student') }}"
           class="block text-center px-4 py-3 rounded-md border border-gray-300 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800 transition">
            <span class="font-medium text-gray-900 dark:text-gray-100">{{ __('I am a Student') }}</span>
        </a>

        <a href="{{ route('register.supervisor') }}"
           class="block text-center px-4 py-3 rounded-md border border-gray-300 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800 transition">
            <span class="font-medium text-gray-900 dark:text-gray-100">{{ __('I am a Supervisor') }}</span>
        </a>
    </div>

    <div class="flex items-center justify-center mt-6">
        <a class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100"
           href="{{ route('login') }}">
            {{ __('Already registered?') }}
        </a>
    </div>
</x-guest-layout>