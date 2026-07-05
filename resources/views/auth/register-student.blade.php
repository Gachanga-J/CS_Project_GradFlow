<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600 dark:text-gray-400">
        {{ __('Your email must follow the pattern: ') }}
        <span class="font-mono">firstnamelastname.stu@gradflow.com</span>
    </div>
    <form method="POST" action="{{ route('register.student') }}">
        @csrf

        <div>
            <x-input-label for="first_name" :value="__('First Name')" />
            <x-text-input id="first_name" class="block mt-1 w-full" type="text" name="first_name" :value="old('first_name')" required autofocus autocomplete="given-name" />
            <x-input-error :messages="$errors->get('first_name')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="last_name" :value="__('Last Name')" />
            <x-text-input id="last_name" class="block mt-1 w-full" type="text" name="last_name" :value="old('last_name')" required autocomplete="family-name" />
            <x-input-error :messages="$errors->get('last_name')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" placeholder="janedoe.stu@gradflow.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="department_id" :value="__('Department')" />
            <select id="department_id" name="department_id" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                <option value="">-- Select Department --</option>
                @foreach ($departments as $department)
                    <option value="{{ $department->id }}" {{ (string) old('department_id') === (string) $department->id ? 'selected' : '' }}>
                        {{ $department->name }}
                    </option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('department_id')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="reg_number" :value="__('Registration Number')" />
            <x-text-input id="reg_number" class="block mt-1 w-full" type="text" name="reg_number" :value="old('reg_number')" required placeholder="e.g. 190280" />
            <x-input-error :messages="$errors->get('reg_number')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="year_of_study" :value="__('Year of Study')" />
            <select id="year_of_study" name="year_of_study" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                <option value="">-- Select Year --</option>
                @foreach ([1, 2, 3, 4, 5, 6] as $year)
                    <option value="{{ $year }}" {{ (string) old('year_of_study') === (string) $year ? 'selected' : '' }}>
                        Year {{ $year }}
                    </option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('year_of_study')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="intake_year" :value="__('Intake Year (Cohort)')" />
            <select id="intake_year" name="intake_year" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                <option value="">-- Select Intake Year --</option>
                @foreach(array_reverse($years) as $year)
                    <option value="{{ $year }}" {{ (string) old('intake_year', now()->year) === (string) $year ? 'selected' : '' }}>
                        {{ $year }} Cohort
                    </option>
                @endforeach
            </select>
            <p class="text-xs text-gray-400 mt-1">The year you were enrolled/started your programme.</p>
            <x-input-error :messages="$errors->get('intake_year')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
            <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900" href="{{ route('login') }}">
                {{ __('Already registered?') }}
            </a>
            <x-primary-button class="ms-4">
                {{ __('Register') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
