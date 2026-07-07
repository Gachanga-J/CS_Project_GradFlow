<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            System Reports
        </h2>
    </x-slot>

    <div class="py-8 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto space-y-8">

        {{-- Department Overview --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                <h3 class="text-sm font-semibold text-gray-800 dark:text-gray-100">Department Overview</h3>
                <p class="text-xs text-gray-400 mt-0.5">All departments — system-wide summary</p>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 dark:bg-gray-700 text-left">
                            <th class="px-4 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400">Department</th>
                            <th class="px-4 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 text-center">Total Students</th>
                            <th class="px-4 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 text-center">Active</th>
                            <th class="px-4 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 text-center">Supervisors</th>
                            <th class="px-4 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 text-center">Milestones</th>
                            <th class="px-4 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 text-center">Submissions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @forelse($deptStats as $dept)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td class="px-4 py-3 font-medium text-gray-800 dark:text-gray-100">{{ $dept['name'] }}</td>
                                <td class="px-4 py-3 text-center text-gray-600 dark:text-gray-300">{{ $dept['total_students'] }}</td>
                                <td class="px-4 py-3 text-center text-emerald-600 font-medium">{{ $dept['active_students'] }}</td>
                                <td class="px-4 py-3 text-center text-gray-600 dark:text-gray-300">{{ $dept['supervisors'] }}</td>
                                <td class="px-4 py-3 text-center text-gray-600 dark:text-gray-300">{{ $dept['total_milestones'] }}</td>
                                <td class="px-4 py-3 text-center text-gray-600 dark:text-gray-300">{{ $dept['total_submissions'] }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center text-sm text-gray-400 italic">
                                    No departments found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Cohort Drill-Down Filters --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <h3 class="text-sm font-semibold text-gray-800 dark:text-gray-100 mb-4">Cohort Drill-Down</h3>
            <form method="GET" action="{{ route('system-administrator.reports.index') }}"
                  class="flex items-end gap-4 flex-wrap">

                {{-- Department --}}
                <div>
                    <label for="department_id" class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">
                        Department
                    </label>
                    <select id="department_id" name="department_id"
                            class="border-gray-300 dark:border-gray-600 dark:bg-gray-700 rounded-md shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500">
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}" {{ $selectedDept == $dept->id ? 'selected' : '' }}>
                                {{ $dept->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Cohort Year --}}
                <div>
                    <label for="intake_year" class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">
                        Cohort (Intake Year)
                    </label>
                    <select id="intake_year" name="intake_year"
                            class="border-gray-300 dark:border-gray-600 dark:bg-gray-700 rounded-md shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500">
                        @forelse($cohortYears as $year)
                            <option value="{{ $year }}" {{ $selectedYear == $year ? 'selected' : '' }}>
                                {{ $year }} Cohort
                            </option>
                        @empty
                            <option value="">No cohorts yet</option>
                        @endforelse
                    </select>
                </div>

                {{-- Year of Study --}}
                <div>
                    <label for="year_of_study" class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">
                        Year of Study
                    </label>
                    <select id="year_of_study" name="year_of_study"
                            class="border-gray-300 dark:border-gray-600 dark:bg-gray-700 rounded-md shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500">
                        @forelse($yearsOfStudy as $year)
                            <option value="{{ $year }}" {{ $selectedYos == $year ? 'selected' : '' }}>
                                Year {{ $year }}
                            </option>
                        @empty
                            <option value="">No data yet</option>
                        @endforelse
                    </select>
                </div>

                <button type="submit"
                        class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700">
                    View Report
                </button>

                @if($selectedDept && $selectedYear && $selectedYos)
                    <a href="{{ route('system-administrator.reports.export', [
                            'department_id' => $selectedDept,
                            'intake_year'   => $selectedYear,
                            'year_of_study' => $selectedYos,
                        ]) }}"
                       class="px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-md hover:bg-emerald-700">
                        Export CSV
                    </a>
                @endif
            </form>

            @if($selectedDept && $selectedYear && $selectedYos)
                <p class="mt-3 text-xs text-gray-400">
                    Showing:
                    <span class="font-medium text-gray-600 dark:text-gray-300">
                        {{ $departments->firstWhere('id', $selectedDept)?->name }}
                        · {{ $selectedYear }} Intake
                        · Year {{ $selectedYos }}
                    </span>
                </p>
            @endif
        </div>

        {{-- Cohort Stats (only when all three filters are selected) --}}
        @if($selectedDept && $selectedYear && $selectedYos)

            {{-- Summary Cards --}}
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-5 text-center">
                    <p class="text-2xl font-bold text-indigo-600">{{ $totalStudents }}</p>
                    <p class="text-xs text-gray-400 mt-1">Students</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-5 text-center">
                    <p class="text-2xl font-bold text-emerald-600">{{ $totalMilestones }}</p>
                    <p class="text-xs text-gray-400 mt-1">Milestones</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-5 text-center">
                    <p class="text-2xl font-bold text-amber-600">{{ $lateCount }}</p>
                    <p class="text-xs text-gray-400 mt-1">Late Submissions</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-5 text-center">
                    <p class="text-2xl font-bold text-gray-700 dark:text-gray-200">{{ number_format($overallAverage, 1) }}</p>
                    <p class="text-xs text-gray-400 mt-1">Cohort Avg Grade</p>
                </div>
            </div>

            {{-- Milestone Completion Rates --}}
            @if($milestoneStats->isNotEmpty())
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <h3 class="text-sm font-semibold text-gray-800 dark:text-gray-100 mb-1">
                        Milestone Completion Rates
                    </h3>
                    <p class="text-xs text-gray-400 mb-5">
                        {{ $departments->firstWhere('id', $selectedDept)?->name }}
                        · {{ $selectedYear }} Intake · Year {{ $selectedYos }}
                        · {{ $totalStudents }} students
                    </p>
                    <div class="space-y-4">
                        @foreach($milestoneStats as $stat)
                            <div>
                                <div class="flex justify-between mb-1">
                                    <span class="text-sm text-gray-700 dark:text-gray-200">
                                        {{ $stat['sequence_order'] }}. {{ $stat['title'] }}
                                    </span>
                                    <span class="text-xs font-semibold {{ $stat['completion_rate'] == 100 ? 'text-emerald-600' : 'text-gray-500' }}">
                                        {{ $stat['submitted'] }}/{{ $totalStudents }} ({{ $stat['completion_rate'] }}%)
                                    </span>
                                </div>
                                <div class="w-full bg-gray-100 dark:bg-gray-700 rounded-full h-2 overflow-hidden">
                                    <div class="h-2 rounded-full {{ $stat['completion_rate'] == 100 ? 'bg-emerald-500' : 'bg-indigo-400' }}"
                                         style="width: {{ $stat['completion_rate'] }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @else
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 text-center">
                    <p class="text-sm text-gray-400 italic">
                        No milestones found for {{ $selectedYear }} intake, Year {{ $selectedYos }}
                        in {{ $departments->firstWhere('id', $selectedDept)?->name }}.
                    </p>
                </div>
            @endif

            {{-- Per-Student Grade Table --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                    <h3 class="text-sm font-semibold text-gray-800 dark:text-gray-100">Student Grade Summary</h3>
                    <span class="text-xs text-gray-400">
                        {{ $selectedYear }} Intake · Year {{ $selectedYos }}
                    </span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-gray-50 dark:bg-gray-700 text-left">
                                <th class="px-4 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400">Student</th>
                                <th class="px-4 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400">Reg No.</th>
                                <th class="px-4 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400">Supervisor</th>
                                <th class="px-4 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 text-center">Submitted</th>
                                <th class="px-4 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 text-center">Awaiting Grading</th>
                                <th class="px-4 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 text-center">Graded</th>
                                <th class="px-4 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 text-center">Late</th>
                                <th class="px-4 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 text-center">Avg Grade</th>
                                <th class="px-4 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 text-center">Completion</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @forelse($cohortStats as $row)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <td class="px-4 py-3 font-medium text-gray-800 dark:text-gray-100">{{ $row['name'] }}</td>
                                    <td class="px-4 py-3 text-gray-500 dark:text-gray-400 font-mono text-xs">{{ $row['reg_number'] }}</td>
                                    <td class="px-4 py-3 text-gray-500 dark:text-gray-400">{{ $row['supervisor'] }}</td>
                                    <td class="px-4 py-3 text-center text-gray-600 dark:text-gray-300">{{ $row['submitted'] }}</td>
                                    <td class="px-4 py-3 text-center">
                                        @if($row['awaiting_grading'] > 0)
                                            <span class="text-amber-600 font-semibold">{{ $row['awaiting_grading'] }}</span>
                                        @else
                                            <span class="text-gray-300">—</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-center text-gray-600 dark:text-gray-300">{{ $row['graded'] }}</td>
                                    <td class="px-4 py-3 text-center">
                                        @if($row['late'] > 0)
                                            <span class="text-orange-600 font-semibold">{{ $row['late'] }}</span>
                                        @else
                                            <span class="text-gray-300">—</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        @if($row['avg_grade'] !== null)
                                            <span class="font-semibold {{ $row['avg_grade'] >= 70 ? 'text-emerald-600' : ($row['avg_grade'] >= 50 ? 'text-amber-600' : 'text-red-600') }}">
                                                {{ number_format($row['avg_grade'], 1) }}
                                            </span>
                                        @else
                                            <span class="text-gray-300">—</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <div class="flex items-center justify-center gap-2">
                                            <div class="w-16 bg-gray-100 dark:bg-gray-700 rounded-full h-1.5 overflow-hidden">
                                                <div class="h-1.5 rounded-full {{ $row['completion_rate'] == 100 ? 'bg-emerald-500' : 'bg-indigo-400' }}"
                                                     style="width: {{ $row['completion_rate'] }}%"></div>
                                            </div>
                                            <span class="text-xs text-gray-500">{{ $row['completion_rate'] }}%</span>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-4 py-8 text-center text-sm text-gray-400 italic">
                                        No students found for this cohort and year of study.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Supervisor Workload --}}
            @if($supervisorStats->isNotEmpty())
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                        <h3 class="text-sm font-semibold text-gray-800 dark:text-gray-100">Supervisor Workload</h3>
                        <span class="text-xs text-gray-400">
                            {{ $selectedYear }} Intake · Year {{ $selectedYos }}
                        </span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="bg-gray-50 dark:bg-gray-700 text-left">
                                    <th class="px-4 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400">Supervisor</th>
                                    <th class="px-4 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 text-center">Students</th>
                                    <th class="px-4 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 text-center">Reviewed</th>
                                    <th class="px-4 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 text-center">Pending</th>
                                    <th class="px-4 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 text-center">Avg Grade Given</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                @foreach($supervisorStats as $sup)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                        <td class="px-4 py-3 font-medium text-gray-800 dark:text-gray-100">{{ $sup['name'] }}</td>
                                        <td class="px-4 py-3 text-center text-gray-600 dark:text-gray-300">{{ $sup['student_count'] }}</td>
                                        <td class="px-4 py-3 text-center text-gray-600 dark:text-gray-300">{{ $sup['reviewed'] }}</td>
                                        <td class="px-4 py-3 text-center">
                                            @if($sup['pending'] > 0)
                                                <span class="text-amber-600 font-semibold">{{ $sup['pending'] }}</span>
                                            @else
                                                <span class="text-gray-300">—</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            @if($sup['avg_grade'] !== null)
                                                <span class="font-semibold {{ $sup['avg_grade'] >= 70 ? 'text-emerald-600' : ($sup['avg_grade'] >= 50 ? 'text-amber-600' : 'text-red-600') }}">
                                                    {{ number_format($sup['avg_grade'], 1) }}
                                                </span>
                                            @else
                                                <span class="text-gray-300">—</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

        @endif

    </div>
</x-app-layout>
