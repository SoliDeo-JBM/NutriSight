<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 md:flex-row md:items-end md:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-emerald-700">NutriSight</p>
                <h2 class="text-2xl font-bold tracking-tight text-slate-900">{{ $roleLabel }} Dashboard</h2>
                <p class="mt-1 text-sm text-slate-500">{{ $focus }}</p>
            </div>
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-900 shadow-sm">
                Marisol Bliss Elementary School
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
            <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                @foreach ($stats as $stat)
                    <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                        <p class="text-sm font-medium text-slate-500">{{ $stat['label'] }}</p>
                        <div class="mt-3 text-4xl font-black tracking-tight text-slate-900">{{ $stat['value'] }}</div>
                        <p class="mt-2 text-sm text-slate-500">{{ $stat['hint'] }}</p>
                    </div>
                @endforeach
            </div>

            <div class="grid gap-6 xl:grid-cols-3">
                <div class="xl:col-span-2 rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <h3 class="text-lg font-semibold text-slate-900">Student profiling preview</h3>
                            <p class="text-sm text-slate-500">Fake records seeded for BMI, sectioning, and nutritional monitoring.</p>
                        </div>
                        <span class="rounded-full bg-amber-50 px-3 py-1 text-xs font-semibold text-amber-800">
                            {{ $nutritionConcernCount }} learners need follow-up
                        </span>
                    </div>

                    <div class="mt-6 overflow-hidden rounded-2xl border border-slate-200">
                        <table class="min-w-full divide-y divide-slate-200 text-sm">
                            <thead class="bg-slate-50 text-left text-xs uppercase tracking-wide text-slate-500">
                                <tr>
                                    <th class="px-4 py-3">Student</th>
                                    <th class="px-4 py-3">Grade / Section</th>
                                    <th class="px-4 py-3">BMI</th>
                                    <th class="px-4 py-3">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 bg-white">
                                @foreach ($students as $student)
                                    <tr>
                                        <td class="px-4 py-3">
                                            <div class="font-semibold text-slate-900">{{ $student->full_name }}</div>
                                            <div class="text-xs text-slate-500">{{ $student->student_number }}</div>
                                        </td>
                                        <td class="px-4 py-3 text-slate-600">{{ $student->grade_level }} - {{ $student->section }}</td>
                                        <td class="px-4 py-3 text-slate-600">{{ $student->latestAssessment?->bmi ?? 'N/A' }}</td>
                                        <td class="px-4 py-3">
                                            <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ in_array($student->latestAssessment?->nutritional_status, ['Severely Wasted', 'Wasted']) ? 'bg-rose-100 text-rose-800' : 'bg-emerald-100 text-emerald-800' }}">
                                                {{ $student->latestAssessment?->nutritional_status ?? 'Pending' }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="space-y-6">
                    <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                        <h3 class="text-lg font-semibold text-slate-900">Nutrition status split</h3>
                        <div class="mt-4 space-y-3">
                            @foreach ($nutritionSummary as $item)
                                <div>
                                    <div class="flex items-center justify-between text-sm">
                                        <span class="text-slate-600">{{ $item->nutritional_status }}</span>
                                        <span class="font-semibold text-slate-900">{{ $item->total }}</span>
                                    </div>
                                    <div class="mt-2 h-2 rounded-full bg-slate-100">
                                        <div class="h-2 rounded-full bg-emerald-500" style="width: {{ max(12, min(100, ($item->total / max(1, $assessmentCount)) * 100)) }}%"></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                        <h3 class="text-lg font-semibold text-slate-900">Today’s attendance and feeding</h3>
                        <div class="mt-4 space-y-4">
                            @foreach ($recentAttendance->take(3) as $record)
                                <div class="rounded-2xl bg-slate-50 px-4 py-3">
                                    <div class="font-medium text-slate-900">{{ $record->student->full_name }}</div>
                                    <div class="text-sm text-slate-600">Attendance: {{ $record->status }} on {{ $record->attendance_date->format('M d, Y') }}</div>
                                </div>
                            @endforeach
                            @foreach ($recentFeedings->take(3) as $record)
                                <div class="rounded-2xl bg-emerald-50 px-4 py-3">
                                    <div class="font-medium text-slate-900">{{ $record->student->full_name }}</div>
                                    <div class="text-sm text-slate-600">{{ $record->meal_type }} - {{ $record->meal_served }}</div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid gap-6 xl:grid-cols-2">
                <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                    <h3 class="text-lg font-semibold text-slate-900">Recent attendance log</h3>
                    <div class="mt-4 space-y-3">
                        @foreach ($recentAttendance as $record)
                            <div class="flex items-center justify-between rounded-2xl bg-slate-50 px-4 py-3">
                                <div>
                                    <div class="font-medium text-slate-900">{{ $record->student->full_name }}</div>
                                    <div class="text-sm text-slate-500">{{ $record->attendance_date->format('M d, Y') }}</div>
                                </div>
                                <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $record->status === 'Present' ? 'bg-emerald-100 text-emerald-800' : ($record->status === 'Late' ? 'bg-amber-100 text-amber-800' : 'bg-rose-100 text-rose-800') }}">
                                    {{ $record->status }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                    <h3 class="text-lg font-semibold text-slate-900">Recent feeding log</h3>
                    <div class="mt-4 space-y-3">
                        @foreach ($recentFeedings as $record)
                            <div class="rounded-2xl bg-slate-50 px-4 py-3">
                                <div class="flex items-center justify-between gap-4">
                                    <div>
                                        <div class="font-medium text-slate-900">{{ $record->student->full_name }}</div>
                                        <div class="text-sm text-slate-500">{{ $record->feeding_date->format('M d, Y') }} - {{ $record->meal_type }}</div>
                                    </div>
                                    <span class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-800">Logged</span>
                                </div>
                                <p class="mt-2 text-sm text-slate-600">{{ $record->meal_served }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>