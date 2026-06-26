@php
    $role = Auth::user()->role;
@endphp

<aside class="w-64 min-h-screen bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700 flex flex-col">

    {{-- User Info --}}
    <div class="px-5 py-5 border-b border-gray-100 dark:border-gray-700">
        <p class="text-sm font-semibold text-gray-800 dark:text-gray-100 truncate">{{ Auth::user()->full_name }}</p>
        <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5 truncate">{{ Auth::user()->email }}</p>
        <span class="inline-block mt-2 text-xs font-medium px-2 py-0.5 rounded-full
            @if($role === 'system_administrator') bg-indigo-100 text-indigo-700 dark:bg-indigo-900 dark:text-indigo-300
            @elseif($role === 'department_coordinator') bg-yellow-100 text-yellow-700 dark:bg-yellow-900 dark:text-yellow-300
            @elseif($role === 'supervisor') bg-emerald-100 text-emerald-700 dark:bg-emerald-900 dark:text-emerald-300
            @elseif($role === 'student') bg-cyan-100 text-cyan-700 dark:bg-cyan-900 dark:text-cyan-300
            @endif">
            {{ ucfirst(str_replace('_', ' ', $role)) }}
        </span>
    </div>

    {{-- Navigation --}}
    <nav class="flex-1 px-3 py-4 space-y-1">

        {{-- Dashboard (all roles) --}}
        <a href="{{ route(Auth::user()->dashboardRoute()) }}"
           class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors
           {{ request()->routeIs(Auth::user()->dashboardRoute()) ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-300' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-gray-100' }}">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/>
                <rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/>
            </svg>
            Dashboard
        </a>

        {{-- System Administrator --}}
        @if($role === 'system_administrator')
            <p class="px-3 pt-4 pb-1 text-xs font-semibold uppercase tracking-widest text-gray-400 dark:text-gray-500">Management</p>

            <a href="{{ route('system-administrator.departments.index') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors
               {{ request()->routeIs('system-administrator.departments.*') ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-300' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-gray-100' }}">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="9" y="2" width="6" height="4" rx="1"/>
                    <rect x="2" y="18" width="6" height="4" rx="1"/>
                    <rect x="16" y="18" width="6" height="4" rx="1"/>
                    <path d="M12 6v4m0 0H5v4m7-4h7v4"/>
                </svg>
                Departments
            </a>

            <a href="{{ route('system-administrator.users.index') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors
               {{ request()->routeIs('system-administrator.users.*') ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-300' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-gray-100' }}">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="7" r="4"/>
                    <path d="M5.5 21a8.38 8.38 0 0 1 13 0"/>
                    <circle cx="5" cy="10" r="2.5"/><path d="M2 20a5 5 0 0 1 5.5-1.5"/>
                    <circle cx="19" cy="10" r="2.5"/><path d="M22 20a5 5 0 0 0-5.5-1.5"/>
                </svg>
                Users
            </a>

            <a href="{{ route('system-administrator.users.create') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors
               {{ request()->routeIs('system-administrator.users.create') ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-300' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-gray-100' }}">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="9" cy="7" r="4"/>
                    <path d="M2 21v-2a4 4 0 0 1 4-4h6a4 4 0 0 1 4 4v2"/>
                    <path d="M19 8v6M22 11h-6"/>
                </svg>
                New User
            </a>

            <p class="px-3 pt-4 pb-1 text-xs font-semibold uppercase tracking-widest text-gray-400 dark:text-gray-500">Configuration</p>

            <a href="{{ route('system-administrator.tags.index') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors
               {{ request()->routeIs('system-administrator.tags.*') ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-300' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-gray-100' }}">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/>
                    <circle cx="7" cy="7" r="1.5"/>
                </svg>
                Research Tags
            </a>
        @endif

        {{-- Department Coordinator --}}
        @if($role === 'department_coordinator')
            <p class="px-3 pt-4 pb-1 text-xs font-semibold uppercase tracking-widest text-gray-400 dark:text-gray-500">Projects</p>

            <a href="{{ route('department-coordinator.projects.index') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors
               {{ request()->routeIs('department-coordinator.projects.*') ? 'bg-yellow-50 text-yellow-700 dark:bg-yellow-900/40 dark:text-yellow-300' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-gray-100' }}">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/>
                </svg>
                Projects
            </a>

            <a href="{{ route('department-coordinator.projects.create') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors
               {{ request()->routeIs('department-coordinator.projects.create') ? 'bg-yellow-50 text-yellow-700 dark:bg-yellow-900/40 dark:text-yellow-300' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-gray-100' }}">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"/><path d="M12 8v8M8 12h8"/>
                </svg>
                New Project
            </a>

            <p class="px-3 pt-4 pb-1 text-xs font-semibold uppercase tracking-widest text-gray-400 dark:text-gray-500">Milestones</p>

            <a href="{{ route('department-coordinator.milestones.index') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors
               {{ request()->routeIs('department-coordinator.milestones.*') ? 'bg-yellow-50 text-yellow-700 dark:bg-yellow-900/40 dark:text-yellow-300' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-gray-100' }}">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M5 22V4"/><path d="M5 4h12l-3 5 3 5H5"/>
                </svg>
                Milestones
            </a>

            <a href="{{ route('department-coordinator.milestones.create') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors
               {{ request()->routeIs('department-coordinator.milestones.create') ? 'bg-yellow-50 text-yellow-700 dark:bg-yellow-900/40 dark:text-yellow-300' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-gray-100' }}">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"/><path d="M12 8v8M8 12h8"/>
                </svg>
                New Milestone
            </a>

            <p class="px-3 pt-4 pb-1 text-xs font-semibold uppercase tracking-widest text-gray-400 dark:text-gray-500">Submissions</p>

            <a href="{{ route('department-coordinator.submissions.index') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors
               {{ request()->routeIs('department-coordinator.submissions.*') ? 'bg-yellow-50 text-yellow-700 dark:bg-yellow-900/40 dark:text-yellow-300' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-gray-100' }}">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M5 2h14"/><path d="M5 22h14"/>
                    <path d="M17 2v4.5a2 2 0 0 1-.586 1.414L12 12l4.414 4.086A2 2 0 0 1 17 17.5V22"/>
                    <path d="M7 2v4.5a2 2 0 0 0 .586 1.414L12 12 7.586 16.086A2 2 0 0 0 7 17.5V22"/>
                </svg>
                Review Submissions
            </a>
        @endif

        {{-- Supervisor --}}
        @if($role === 'supervisor')
            <p class="px-3 pt-4 pb-1 text-xs font-semibold uppercase tracking-widest text-gray-400 dark:text-gray-500">My Students</p>

            <a href="{{ route('supervisor.students.index') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors
               {{ request()->routeIs('supervisor.students.*') ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-gray-100' }}">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="7" r="4"/>
                    <path d="M5.5 21a8.38 8.38 0 0 1 13 0"/>
                </svg>
                Students & Projects
            </a>

            <p class="px-3 pt-4 pb-1 text-xs font-semibold uppercase tracking-widest text-gray-400 dark:text-gray-500">Submissions</p>

            <a href="{{ route('supervisor.submissions.index') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors
               {{ request()->routeIs('supervisor.submissions.*') ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-gray-100' }}">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                    <polyline points="14 2 14 8 20 8"/>
                    <line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/>
                </svg>
                Review Submissions
            </a>

            <p class="px-3 pt-4 pb-1 text-xs font-semibold uppercase tracking-widest text-gray-400 dark:text-gray-500">Profile</p>

            <a href="{{ route('supervisor.profile.edit') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors
               {{ request()->routeIs('supervisor.profile.*') ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-gray-100' }}">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/>
                    <circle cx="7" cy="7" r="1.5"/>
                </svg>
                Research Interests
            </a>
        @endif

        {{-- Student --}}
        @if($role === 'student')
            <p class="px-3 pt-4 pb-1 text-xs font-semibold uppercase tracking-widest text-gray-400 dark:text-gray-500">Supervisors</p>

            <a href="{{ route('student.supervisor-matches') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors
               {{ request()->routeIs('student.supervisor-matches') ? 'bg-cyan-50 text-cyan-700 dark:bg-cyan-900/40 dark:text-cyan-300' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-gray-100' }}">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="3"/>
                    <path d="M12 2v2M12 20v2M4.22 4.22l1.42 1.42M18.36 18.36l1.42 1.42M2 12h2M20 12h2M4.22 19.78l1.42-1.42M18.36 5.64l1.42-1.42"/>
                </svg>
                Supervisor Matches
            </a>
        @endif

    </nav>

    {{-- Bottom: Profile & Logout --}}
    <div class="px-3 py-4 border-t border-gray-100 dark:border-gray-700 space-y-1">
        <a href="{{ route('profile.edit') }}"
           class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors
           {{ request()->routeIs('profile.edit') ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-300' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-gray-100' }}">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="8" r="4"/><path d="M4 20a8 8 0 0 1 16 0"/>
            </svg>
            Profile
        </a>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
               class="w-full flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors text-gray-600 dark:text-gray-400 hover:bg-red-50 hover:text-red-600 dark:hover:bg-red-900/30 dark:hover:text-red-400">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                    <polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/>
                </svg>
                Log Out
            </button>
        </form>
    </div>

</aside>
