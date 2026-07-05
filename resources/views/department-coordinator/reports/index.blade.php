<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Cohort Report') }}
        </h2>
    </x-slot>

    <div class="py-8 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto space-y-6">

        @if(session('success'))
            <div class="p-4 bg-green-100 text-green-700 rounded border border-green-300 text-sm">
                {{ session('success') }}
            </div>
        @endif

        {{-- Filters --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <form method="GET" action="{{ route('department-coordinator.reports.index') }}"
                  class="flex items-end gap-4 flex-wrap">

                {{-- Intake Year --}}
                <div>
                    <label for="intake_year" class="block text-sm font-medium text-gray-700 mb-1">
                        Cohort (Intake Year)
                    </label>
                    <select id="intake_year" name="intake_year"
                            class="border-gray-300 rounded-md shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500">
                        @foreach($cohortYears as $year)
                            <option value="{{ $year }}" {{ $selectedYear == $year ? 'selected' : '' }}>
                                {{ $year }} Intake
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Year of Study --}}
                <div>
                    <label for="year_of_study" class="block text-sm font-medium text-gray-700 mb-1">
                        Year of Study
                    </label>
                    <select id="year_of_study" name="year_of_study"
                            class="border-gray-300 rounded-md shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500">
                        @foreach($yearsOfStudy as $year)
                            <option value="{{ $year }}" {{ $selectedYearOfStudy == $year ? 'selected' : '' }}>
                                Year {{ $year }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <button type="submit"
                        class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700">
                    View Report
                </button>

                {{-- CSV Export --}}
                <a href="{{ route('department-coordinator.reports.index', [
                        'intake_year'   => $selectedYear,
                        'year_of_study' => $selectedYearOfStudy,
                        'export'        => 'csv'
                    ]) }}"
                   class="px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-md hover:bg-emerald-700">
                    Export CSV
                </a>
            </form>

            {{-- Active filter label --}}
            @if($selectedYear && $selectedYearOfStudy)
                <p class="mt-3 text-xs text-gray-400">
                    Showing: <span class="font-medium text-gray-600">{{ $selectedYear }} Intake — Year {{ $selectedYearOfStudy }} students</span>
                </p>
            @endif
        </div>

        @if($totalStudents === 0 && $totalMilestones === 0)
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-10 text-center">
                <p class="text-sm text-gray-400 italic">
                    No data found for {{ $selectedYear }} intake, Year {{ $selectedYearOfStudy }} students in this department.
                </p>
                <p class="text-xs text-gray-300 mt-1">
                    Make sure milestones have been created for this cohort and year of study.
                </p>
            </div>
        @else

            {{-- Summary Cards --}}
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 text-center">
                    <p class="text-2xl font-bold text-indigo-600">{{ $totalStudents }}</p>
                    <p class="text-xs text-gray-400 mt-1">Students</p>
                </div>
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 text-center">
                    <p class="text-2xl font-bold text-emerald-600">{{ $totalMilestones }}</p>
                    <p class="text-xs text-gray-400 mt-1">Milestones</p>
                </div>
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 text-center">
                    <p class="text-2xl font-bold text-amber-600">{{ $lateSubmissionsCount }}</p>
                    <p class="text-xs text-gray-400 mt-1">Late Submissions</p>
                </div>
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 text-center">
                    <p class="text-2xl font-bold text-gray-700">{{ number_format($overallAverage, 1) }}</p>
                    <p class="text-xs text-gray-400 mt-1">Cohort Avg Grade</p>
                </div>
            </div>

            {{-- Milestone Completion Rates --}}
            @if($milestoneStats->isNotEmpty())
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-sm font-semibold text-gray-800 mb-1">Milestone Completion Rates</h3>
                    <p class="text-xs text-gray-400 mb-5">
                        {{ $selectedYear }} Intake · Year {{ $selectedYearOfStudy }} · {{ $totalStudents }} students
                    </p>
                    <div class="space-y-4">
                        @foreach($milestoneStats as $stat)
                            <div>
                                <div class="flex justify-between mb-1">
                                    <span class="text-sm text-gray-700">
                                        {{ $stat['sequence_order'] }}. {{ $stat['title'] }}
                                    </span>
                                    <span class="text-xs font-semibold {{ $stat['completion_rate'] == 100 ? 'text-emerald-600' : 'text-gray-500' }}">
                                        {{ $stat['submitted'] }}/{{ $totalStudents }} ({{ $stat['completion_rate'] }}%)
                                    </span>
                                </div>
                                <div class="w-full bg-gray-100 rounded-full h-2 overflow-hidden">
                                    <div class="h-2 rounded-full {{ $stat['completion_rate'] == 100 ? 'bg-emerald-500' : 'bg-indigo-400' }}"
                                         style="width: {{ $stat['completion_rate'] }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @else
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 text-center">
                    <p class="text-sm text-gray-400 italic">
                        No milestones found for {{ $selectedYear }} intake, Year {{ $selectedYearOfStudy }}.
                    </p>
                    <p class="text-xs text-gray-300 mt-1">
                        Create milestones with this cohort and year of study to see completion rates.
                    </p>
                </div>
            @endif

            {{-- Per-Student Grade Table --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="text-sm font-semibold text-gray-800">
                        Student Grade Summary
                    </h3>
                    <span class="text-xs text-gray-400">
                        {{ $selectedYear }} Intake · Year {{ $selectedYearOfStudy }}
                    </span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-gray-50 text-left">
                                <th class="px-4 py-3 text-xs font-semibold text-gray-500">Student</th>
                                <th class="px-4 py-3 text-xs font-semibold text-gray-500">Reg No.</th>
                                <th class="px-4 py-3 text-xs font-semibold text-gray-500">Supervisor</th>
                                <th class="px-4 py-3 text-xs font-semibold text-gray-500 text-center">Submitted</th>
                                <th class="px-4 py-3 text-xs font-semibold text-gray-500 text-center">Awaiting Grading</th>
                                <th class="px-4 py-3 text-xs font-semibold text-gray-500 text-center">Graded</th>
                                <th class="px-4 py-3 text-xs font-semibold text-gray-500 text-center">Late</th>
                                <th class="px-4 py-3 text-xs font-semibold text-gray-500 text-center">Avg Grade</th>
                                <th class="px-4 py-3 text-xs font-semibold text-gray-500 text-center">Completion</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($studentStats as $row)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 font-medium text-gray-800">{{ $row['name'] }}</td>
                                    <td class="px-4 py-3 text-gray-500 font-mono text-xs">{{ $row['reg_number'] }}</td>
                                    <td class="px-4 py-3 text-gray-500">{{ $row['supervisor'] }}</td>
                                    <td class="px-4 py-3 text-center text-gray-600">{{ $row['submitted'] }}</td>
                                    <td class="px-4 py-3 text-center">
                                        @if($row['awaiting_grading'] > 0)
                                            <span class="text-amber-600 font-semibold">{{ $row['awaiting_grading'] }}</span>
                                        @else
                                            <span class="text-gray-300">—</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-center text-gray-600">{{ $row['graded'] }}</td>
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
                                            <div class="w-16 bg-gray-100 rounded-full h-1.5 overflow-hidden">
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
            @if($supervisorWorkload->isNotEmpty())
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                        <h3 class="text-sm font-semibold text-gray-800">Supervisor Workload</h3>
                        <span class="text-xs text-gray-400">
                            {{ $selectedYear }} Intake · Year {{ $selectedYearOfStudy }}
                        </span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="bg-gray-50 text-left">
                                    <th class="px-4 py-3 text-xs font-semibold text-gray-500">Supervisor</th>
                                    <th class="px-4 py-3 text-xs font-semibold text-gray-500 text-center">Students</th>
                                    <th class="px-4 py-3 text-xs font-semibold text-gray-500 text-center">Reviewed</th>
                                    <th class="px-4 py-3 text-xs font-semibold text-gray-500 text-center">Pending</th>
                                    <th class="px-4 py-3 text-xs font-semibold text-gray-500 text-center">Avg Grade Given</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($supervisorWorkload as $sup)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-3 font-medium text-gray-800">{{ $sup['name'] }}</td>
                                        <td class="px-4 py-3 text-center text-gray-600">{{ $sup['student_count'] }}</td>
                                        <td class="px-4 py-3 text-center text-gray-600">{{ $sup['reviewed'] }}</td>
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
