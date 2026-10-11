<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-bold text-2xl text-slate-800 leading-tight">
                    {{ __('Clinical Case Dashboard') }}
                </h2>
                <p class="text-sm text-slate-500 mt-0.5">Overview of active surgical cases, implant requisitions, and review status</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('cases.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-slate-300 rounded-lg text-sm font-medium text-slate-700 shadow-sm hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-offset-2 transition">
                    <svg class="w-4 h-4 me-1.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                    Browse All Cases
                </a>
                @can('create', App\Models\MedicalCase::class)
                    <a href="{{ route('cases.create') }}" class="inline-flex items-center px-4 py-2 bg-teal-600 border border-transparent rounded-lg text-sm font-medium text-white shadow-sm hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-offset-2 transition">
                        <svg class="w-4 h-4 me-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        New Case
                    </a>
                @endcan
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

            <!-- Metrics Overview Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-5">
                <!-- Total Cases -->
                <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm hover:shadow-md transition">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold uppercase tracking-wider text-slate-500">Total Cases</span>
                        <div class="w-8 h-8 rounded-lg bg-slate-100 flex items-center justify-center text-slate-600">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        </div>
                    </div>
                    <div class="mt-4 flex items-baseline justify-between">
                        <div class="text-3xl font-extrabold text-slate-900" id="metric-total-cases">{{ $totalCases }}</div>
                        <a href="{{ route('cases.index') }}" class="text-xs text-teal-600 font-medium hover:underline">View all &rarr;</a>
                    </div>
                </div>

                <!-- Planning Cases -->
                <div class="bg-white rounded-xl border border-amber-200 p-5 shadow-sm hover:shadow-md transition">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold uppercase tracking-wider text-amber-700">Planning</span>
                        <div class="w-8 h-8 rounded-lg bg-amber-50 flex items-center justify-center text-amber-600">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </div>
                    </div>
                    <div class="mt-4 flex items-baseline justify-between">
                        <div class="text-3xl font-extrabold text-amber-900" id="metric-planning-cases">{{ $planningCases }}</div>
                        <a href="{{ route('cases.index', ['status' => 'Planning']) }}" class="text-xs text-amber-700 font-medium hover:underline">Filter &rarr;</a>
                    </div>
                </div>

                <!-- In Review Cases -->
                <div class="bg-white rounded-xl border border-indigo-200 p-5 shadow-sm hover:shadow-md transition">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold uppercase tracking-wider text-indigo-700">In Review</span>
                        <div class="w-8 h-8 rounded-lg bg-indigo-50 flex items-center justify-center text-indigo-600">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        </div>
                    </div>
                    <div class="mt-4 flex items-baseline justify-between">
                        <div class="text-3xl font-extrabold text-indigo-900" id="metric-in-review-cases">{{ $inReviewCases }}</div>
                        <a href="{{ route('cases.index', ['status' => 'In Review']) }}" class="text-xs text-indigo-700 font-medium hover:underline">Filter &rarr;</a>
                    </div>
                </div>

                <!-- Approved Cases -->
                <div class="bg-white rounded-xl border border-emerald-200 p-5 shadow-sm hover:shadow-md transition">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold uppercase tracking-wider text-emerald-700">Approved</span>
                        <div class="w-8 h-8 rounded-lg bg-emerald-50 flex items-center justify-center text-emerald-600">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                    </div>
                    <div class="mt-4 flex items-baseline justify-between">
                        <div class="text-3xl font-extrabold text-emerald-900" id="metric-approved-cases">{{ $approvedCases }}</div>
                        <a href="{{ route('cases.index', ['status' => 'Approved']) }}" class="text-xs text-emerald-700 font-medium hover:underline">Filter &rarr;</a>
                    </div>
                </div>

                <!-- Completed Cases -->
                <div class="bg-white rounded-xl border border-teal-200 p-5 shadow-sm hover:shadow-md transition">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold uppercase tracking-wider text-teal-700">Completed</span>
                        <div class="w-8 h-8 rounded-lg bg-teal-50 flex items-center justify-center text-teal-600">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        </div>
                    </div>
                    <div class="mt-4 flex items-baseline justify-between">
                        <div class="text-3xl font-extrabold text-teal-900" id="metric-completed-cases">{{ $completedCases }}</div>
                        <a href="{{ route('cases.index', ['status' => 'Completed']) }}" class="text-xs text-teal-700 font-medium hover:underline">Filter &rarr;</a>
                    </div>
                </div>
            </div>

            <!-- Role & Quick Information Banner -->
            <div class="bg-gradient-to-r from-teal-50 via-slate-50 to-white border border-teal-100 rounded-xl p-5 flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-teal-600 text-white flex items-center justify-center font-bold text-sm">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </div>
                    <div>
                        <div class="font-semibold text-slate-800">
                            Welcome back, {{ Auth::user()->name }}
                        </div>
                        <div class="text-xs text-slate-500">
                            Authorized Role: <span class="font-semibold text-slate-700">{{ Auth::user()->role }}</span> &bull;
                            @if(Auth::user()->isAdmin())
                                You have full administrative capabilities to create, edit, change status, and delete case records.
                            @else
                                You have read-only permissions to inspect clinical cases and view case documentation.
                            @endif
                        </div>
                    </div>
                </div>
                <div class="text-xs text-slate-500 bg-white px-3 py-2 rounded-lg border border-slate-200">
                    <span class="font-medium text-slate-700">Database Source:</span> MySQL &bull; <span class="font-medium text-slate-700">Records:</span> Live
                </div>
            </div>

            <!-- Recent Cases Table -->
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between">
                    <div>
                        <h3 class="font-bold text-base text-slate-800">Recent Medical Cases</h3>
                        <p class="text-xs text-slate-500">Latest active cases recorded in Jajal Medical System</p>
                    </div>
                    <a href="{{ route('cases.index') }}" class="text-sm font-semibold text-teal-600 hover:text-teal-700">
                        View All Cases &rarr;
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                        <thead class="bg-slate-50 text-slate-600 text-xs uppercase tracking-wider font-semibold">
                            <tr>
                                <th class="px-6 py-3.5 text-left">Case #</th>
                                <th class="px-6 py-3.5 text-left">Patient Ref</th>
                                <th class="px-6 py-3.5 text-left">Surgeon</th>
                                <th class="px-6 py-3.5 text-left">Implant Type</th>
                                <th class="px-6 py-3.5 text-left">Status</th>
                                <th class="px-6 py-3.5 text-left">Priority</th>
                                <th class="px-6 py-3.5 text-left">Surgery Date</th>
                                <th class="px-6 py-3.5 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            @forelse($recentCases as $case)
                                <tr class="hover:bg-slate-50 transition">
                                    <td class="px-6 py-4 font-semibold text-slate-900 whitespace-nowrap">
                                        <a href="{{ route('cases.show', $case) }}" class="text-teal-600 hover:text-teal-800 hover:underline">
                                            {{ $case->case_number }}
                                        </a>
                                    </td>
                                    <td class="px-6 py-4 text-slate-600 whitespace-nowrap font-mono text-xs">
                                        {{ $case->patient_reference }}
                                    </td>
                                    <td class="px-6 py-4 text-slate-800 whitespace-nowrap font-medium">
                                        {{ $case->surgeon_name }}
                                    </td>
                                    <td class="px-6 py-4 text-slate-600 max-w-xs truncate">
                                        {{ $case->implant_type }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $statusClasses = [
                                                'Draft' => 'bg-slate-100 text-slate-700 border-slate-200',
                                                'Planning' => 'bg-amber-100 text-amber-800 border-amber-200',
                                                'In Review' => 'bg-indigo-100 text-indigo-800 border-indigo-200',
                                                'Approved' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                                                'Completed' => 'bg-teal-100 text-teal-800 border-teal-200',
                                                'Cancelled' => 'bg-rose-100 text-rose-800 border-rose-200',
                                            ][$case->status] ?? 'bg-slate-100 text-slate-700 border-slate-200';
                                        @endphp
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold border {{ $statusClasses }}">
                                            {{ $case->status }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $priorityClasses = [
                                                'Low' => 'text-slate-600 bg-slate-50 border-slate-200',
                                                'Medium' => 'text-amber-700 bg-amber-50 border-amber-200',
                                                'High' => 'text-rose-700 bg-rose-50 border-rose-200 font-bold',
                                            ][$case->priority] ?? 'text-slate-600 bg-slate-50 border-slate-200';
                                        @endphp
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs border {{ $priorityClasses }}">
                                            {{ $case->priority }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-slate-600 whitespace-nowrap">
                                        {{ $case->surgery_date ? $case->surgery_date->format('M d, Y') : '—' }}
                                    </td>
                                    <td class="px-6 py-4 text-right whitespace-nowrap text-xs font-medium space-x-2">
                                        <a href="{{ route('cases.show', $case) }}" class="text-teal-600 hover:text-teal-900">View</a>
                                        @can('update', $case)
                                            <a href="{{ route('cases.edit', $case) }}" class="text-indigo-600 hover:text-indigo-900">Edit</a>
                                        @endcan
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-6 py-8 text-center text-slate-500">
                                        No medical cases found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
