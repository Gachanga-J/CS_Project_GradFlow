<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>GradFlow — Academic Project Supervision</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 antialiased">

    <!-- Top Navigation -->
    <header class="border-b border-gray-200 dark:border-gray-800">
        <div class="max-w-6xl mx-auto px-6 py-4 flex items-center justify-between">

            <!-- Logo -->
            <a href="/" class="flex items-center gap-2">
                <svg class="h-8 w-8 text-indigo-600 dark:text-indigo-400"
                     fill="none"
                     viewBox="0 0 24 24"
                     stroke="currentColor"
                     stroke-width="1.8">
                    <path stroke-linecap="round"
                          stroke-linejoin="round"
                          d="M12 3 1 8l11 5 9-4.09V17h2V8L12 3Z" />
                    <path stroke-linecap="round"
                          stroke-linejoin="round"
                          d="M5 13.18v4L12 21l7-3.82v-4" />
                </svg>

                <span class="text-xl font-bold tracking-tight">
                    GradFlow
                </span>
            </a>

            <!-- Navigation -->
            @if (Route::has('login'))
                <nav class="flex items-center gap-3">

                    @auth
                        <a href="{{ route(auth()->user()->dashboardRoute()) }}"
                           class="px-4 py-2 rounded-md bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold transition shadow">
                            Dashboard
                        </a>
                    @else

                        <!-- Login -->
                        <a href="{{ route('login') }}"
                           class="text-sm font-medium text-gray-700 dark:text-gray-300 hover:text-indigo-600 dark:hover:text-indigo-400 transition">
                            Log in
                        </a>

                        <!-- Register -->
                        <a href="{{ route('register') }}"
                            class="px-4 py-2 rounded-md bg-indigo-600 hover:bg-indigo-700 !text-white text-sm font-semibold transition shadow">
                            Register
                        </a>
                    @endauth

                </nav>
            @endif

        </div>
    </header>

    <!-- Hero -->
    <main class="max-w-6xl mx-auto px-6">

        <!-- Hero -->
<section class="py-24 flex flex-col items-center text-center">

    <div class="max-w-3xl">

        <h1 class="text-4xl sm:text-5xl font-extrabold tracking-tight leading-tight">
            Academic project supervision,
            <span class="text-indigo-600 dark:text-indigo-400">
                finally streamlined.
            </span>
        </h1>

        <p class="mt-6 text-lg text-gray-600 dark:text-gray-400">
            GradFlow replaces fragmented emails, spreadsheets, and missed deadlines with a
            centralised, state-gated workflow — connecting students, supervisors, and
            department coordinators in one transparent pipeline.
        </p>

        <div class="mt-10 flex justify-center gap-4">

            @auth

                <a href="{{ route(auth()->user()->dashboardRoute()) }}"
                   class="inline-flex items-center px-6 py-3 rounded-md bg-indigo-600 hover:bg-indigo-700 !text-white font-semibold transition shadow">
                    Go to Dashboard
                </a>

            @else

                <a href="{{ route('register') }}"
                   class="inline-flex items-center px-6 py-3 rounded-md bg-indigo-600 hover:bg-indigo-700 !text-white font-semibold transition shadow">
                    Get Started
                </a>

            @endauth

        </div>

    </div>

</section>

        <!-- Feature Cards -->
        <section class="py-16 grid md:grid-cols-3 gap-8 border-t border-gray-200 dark:border-gray-800">

            <!-- Feature 1 -->
            <div>

                <svg class="h-10 w-10 text-indigo-600 dark:text-indigo-400 mb-4"
                     fill="none"
                     viewBox="0 0 24 24"
                     stroke="currentColor"
                     stroke-width="1.8">

                    <path stroke-linecap="round"
                          stroke-linejoin="round"
                          d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />

                </svg>

                <h3 class="font-semibold text-lg">
                    Sequential Milestones
                </h3>

                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                    Automated state-gates ensure students can't skip ahead until each milestone
                    is reviewed and approved.
                </p>

            </div>

            <!-- Feature 2 -->
            <div>

                <svg class="h-10 w-10 text-indigo-600 dark:text-indigo-400 mb-4"
                     fill="none"
                     viewBox="0 0 24 24"
                     stroke="currentColor"
                     stroke-width="1.8">

                    <path stroke-linecap="round"
                          stroke-linejoin="round"
                          d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z" />

                </svg>

                <h3 class="font-semibold text-lg">
                    Smart Supervisor Matching
                </h3>

                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                    Tag-based compatibility scoring connects students with supervisors whose
                    research interests align with their project.
                </p>

            </div>

            <!-- Feature 3 -->
            <div>

                <svg class="h-10 w-10 text-indigo-600 dark:text-indigo-400 mb-4"
                     fill="none"
                     viewBox="0 0 24 24"
                     stroke="currentColor"
                     stroke-width="1.8">

                    <path stroke-linecap="round"
                          stroke-linejoin="round"
                          d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z" />

                </svg>

                <h3 class="font-semibold text-lg">
                    Real-Time Analytics
                </h3>

                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                    Coordinators get cohort-wide dashboards showing student progress and
                    supervisor workloads at a glance.
                </p>

            </div>

        </section>

    </main>

    <!-- Footer -->
    <footer class="border-t border-gray-200 dark:border-gray-800 py-6 text-center text-sm text-gray-500">

        &copy; {{ date('Y') }} GradFlow — Strathmore University

    </footer>

</body>
</html>