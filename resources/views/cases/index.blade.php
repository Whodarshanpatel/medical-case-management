<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-bold text-2xl text-slate-800 leading-tight">
                    {{ __('Medical Cases') }}
                </h2>
                <p class="text-sm text-slate-500 mt-0.5">Manage and track surgical cases, clinical reviews, and implant specifications</p>
            </div>
            @can('create', App\Models\MedicalCase::class)
                <div>
                    <a href="{{ route('cases.create') }}" class="inline-flex items-center px-4 py-2.5 bg-teal-600 border border-transparent rounded-lg text-sm font-semibold text-white shadow-sm hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-offset-2 transition" id="btn-create-case">
                        <svg class="w-4 h-4 me-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Create New Case
                    </a>
                </div>
            @endcan
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <!-- Flash Session Messages -->
            @if(session('success'))
                <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-start gap-3 shadow-sm" role="alert">
                    <svg class="w-5 h-5 text-emerald-600 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <div class="text-sm font-medium">{{ session('success') }}</div>
                </div>
            @endif

            @if(session('error'))
                <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 flex items-start gap-3 shadow-sm" role="alert">
                    <svg class="w-5 h-5 text-rose-600 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <div class="text-sm font-medium">{{ session('error') }}</div>
                </div>
            @endif

            <!-- Search and Filter Bar -->
            <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-sm">
                <form method="GET" action="{{ route('cases.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-center">
                    <!-- Search Input -->
                    <div class="sm:col-span-6 relative">
                        <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>
                        <input
                            type="text"
                            name="search"
                            id="search-input"
                            value="{{ $search }}"
                            placeholder="Search by Case Number or Surgeon Name..."
                            class="block w-full ps-9 pe-3 py-2 text-sm border-slate-300 rounded-lg focus:ring-teal-500 focus:border-teal-500 placeholder-slate-400"
                        />
                    </div>

                    <!-- Status Filter Dropdown -->
                    <div class="sm:col-span-4">
                        <select
                            name="status"
                            id="status-filter"
                            class="block w-full py-2 px-3 text-sm border-slate-300 rounded-lg focus:ring-teal-500 focus:border-teal-500 text-slate-700"
                        >
                            <option value="">All Statuses</option>
                            @foreach($statuses as $st)
                                <option value="{{ $st }}" {{ $status === $st ? 'selected' : '' }}>
                                    {{ $st }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Filter Actions -->
                    <div class="sm:col-span-2 flex items-center gap-2">
                        <button
                            type="submit"
                            id="btn-filter"
                            class="w-full inline-flex justify-center items-center px-3.5 py-2 bg-slate-800 text-white text-sm font-medium rounded-lg hover:bg-slate-900 focus:outline-none focus:ring-2 focus:ring-slate-700 transition"
                        >
                            Filter
                        </button>
                        @if($search || $status)
                            <a
                                href="{{ route('cases.index') }}"
                                class="inline-flex items-center px-3 py-2 text-xs font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-lg transition"
                                title="Clear filters"
                            >
                                Reset
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            <!-- Cases Table Card -->
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                        <thead class="bg-slate-50 text-slate-600 text-xs uppercase tracking-wider font-semibold">
                            <tr>
                                <th scope="col" class="px-6 py-3.5 text-left">Case Number</th>
                                <th scope="col" class="px-6 py-3.5 text-left">Patient Ref</th>
                                <th scope="col" class="px-6 py-3.5 text-left">Surgeon Name</th>
                                <th scope="col" class="px-6 py-3.5 text-left">Implant Type</th>
                                <th scope="col" class="px-6 py-3.5 text-left">Status</th>
                                <th scope="col" class="px-6 py-3.5 text-left">Priority</th>
                                <th scope="col" class="px-6 py-3.5 text-left">Surgery Date</th>
                                <th scope="col" class="px-6 py-3.5 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            @forelse($cases as $case)
                                <tr class="hover:bg-slate-50/80 transition">
                                    <!-- Case Number -->
                                    <td class="px-6 py-4 font-semibold text-slate-900 whitespace-nowrap">
                                        <a href="{{ route('cases.show', $case) }}" class="text-teal-600 hover:text-teal-800 hover:underline">
                                            {{ $case->case_number }}
                                        </a>
                                    </td>

                                    <!-- Patient Reference (Dummy) -->
                                    <td class="px-6 py-4 text-slate-600 font-mono text-xs whitespace-nowrap">
                                        {{ $case->patient_reference }}
                                    </td>

                                    <!-- Surgeon Name -->
                                    <td class="px-6 py-4 text-slate-800 whitespace-nowrap font-medium">
                                        {{ $case->surgeon_name }}
                                    </td>

                                    <!-- Implant Type -->
                                    <td class="px-6 py-4 text-slate-600 max-w-xs truncate" title="{{ $case->implant_type }}">
                                        {{ $case->implant_type }}
                                    </td>

                                    <!-- Status Badge -->
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

                                    <!-- Priority Badge -->
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

                                    <!-- Surgery Date -->
                                    <td class="px-6 py-4 text-slate-600 whitespace-nowrap text-xs">
                                        {{ $case->surgery_date ? $case->surgery_date->format('M d, Y') : '—' }}
                                    </td>

                                    <!-- Actions -->
                                    <td class="px-6 py-4 text-right whitespace-nowrap text-xs font-medium space-x-2">
                                        <a href="{{ route('cases.show', $case) }}" class="text-teal-600 hover:text-teal-900 font-medium">
                                            Details
                                        </a>

                                        @can('update', $case)
                                            <a href="{{ route('cases.edit', $case) }}" class="text-indigo-600 hover:text-indigo-900 font-medium">
                                                Edit
                                            </a>
                                        @endcan

                                        @can('delete', $case)
                                            <form action="{{ route('cases.destroy', $case) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete case {{ $case->case_number }}? This action cannot be undone.');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-rose-600 hover:text-rose-900 font-medium">
                                                    Delete
                                                </button>
                                            </form>
                                        @endcan
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-6 py-12 text-center">
                                        <div class="max-w-sm mx-auto">
                                            <div class="w-12 h-12 mx-auto rounded-full bg-slate-100 flex items-center justify-center text-slate-400 mb-3">
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                            </div>
                                            <p class="font-semibold text-slate-700">No medical cases found</p>
                                            <p class="text-xs text-slate-500 mt-1">Try adjusting your search criteria or filter options.</p>
                                            @can('create', App\Models\MedicalCase::class)
                                                <div class="mt-4">
                                                    <a href="{{ route('cases.create') }}" class="inline-flex items-center px-3 py-1.5 bg-teal-600 text-white rounded-md text-xs font-semibold hover:bg-teal-700 transition">
                                                        Create Case
                                                    </a>
                                                </div>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($cases->hasPages())
                    <div class="px-6 py-4 border-t border-slate-200 bg-slate-50/50">
                        {{ $cases->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
