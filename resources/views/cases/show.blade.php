<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 text-xs text-slate-500 mb-1">
                    <a href="{{ route('cases.index') }}" class="hover:text-teal-600">Cases</a>
                    <span>&rsaquo;</span>
                    <span class="font-mono">{{ $case->case_number }}</span>
                </div>
                <div class="flex items-center gap-3">
                    <h2 class="font-bold text-2xl text-slate-800 leading-tight">
                        Case: {{ $case->case_number }}
                    </h2>
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
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold border {{ $statusClasses }}">
                        {{ $case->status }}
                    </span>
                </div>
            </div>

            <!-- Header Actions based on Permissions -->
            <div class="flex items-center gap-2.5">
                <a href="{{ route('cases.index') }}" class="inline-flex items-center px-3.5 py-2 bg-white border border-slate-300 rounded-lg text-sm font-medium text-slate-700 shadow-sm hover:bg-slate-50 transition">
                    &larr; Back to Cases
                </a>

                @can('update', $case)
                    <a href="{{ route('cases.edit', $case) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-lg text-sm font-semibold text-white shadow-sm hover:bg-indigo-700 transition">
                        <svg class="w-4 h-4 me-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        Edit Case
                    </a>
                @endcan

                @can('delete', $case)
                    <form action="{{ route('cases.destroy', $case) }}" method="POST" onsubmit="return confirm('Are you sure you want to permanently delete case {{ $case->case_number }}?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex items-center px-3.5 py-2 bg-rose-50 border border-rose-200 rounded-lg text-sm font-semibold text-rose-700 hover:bg-rose-100 transition">
                            <svg class="w-4 h-4 me-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            Delete
                        </button>
                    </form>
                @endcan
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <!-- Flash Session Messages -->
            @if(session('success'))
                <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-start gap-3 shadow-sm" role="alert">
                    <svg class="w-5 h-5 text-emerald-600 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <div class="text-sm font-medium">{{ session('success') }}</div>
                </div>
            @endif

            <!-- Main Details Card -->
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-6 py-5 border-b border-slate-200 bg-slate-50/50 flex items-center justify-between">
                    <div>
                        <h3 class="font-bold text-slate-800 text-base">Clinical Requisition Details</h3>
                        <p class="text-xs text-slate-500">Record ID #{{ $case->id }} in Jajal Medical Case Database</p>
                    </div>
                    <div>
                        @php
                            $priorityClasses = [
                                'Low' => 'text-slate-600 bg-slate-100 border-slate-200',
                                'Medium' => 'text-amber-800 bg-amber-100 border-amber-200',
                                'High' => 'text-rose-800 bg-rose-100 border-rose-200 font-bold',
                            ][$case->priority] ?? 'text-slate-600 bg-slate-100 border-slate-200';
                        @endphp
                        <span class="inline-flex items-center px-2.5 py-1 rounded text-xs border {{ $priorityClasses }}">
                            Priority: {{ $case->priority }}
                        </span>
                    </div>
                </div>

                <div class="p-6">
                    <dl class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">

                        <!-- Case Number -->
                        <div class="bg-slate-50/70 p-4 rounded-lg border border-slate-100">
                            <dt class="text-xs font-semibold uppercase tracking-wider text-slate-500">Case Number</dt>
                            <dd class="mt-1 text-base font-bold text-slate-900 font-mono" id="detail-case-number">{{ $case->case_number }}</dd>
                        </div>

                        <!-- Patient Reference -->
                        <div class="bg-slate-50/70 p-4 rounded-lg border border-slate-100">
                            <dt class="text-xs font-semibold uppercase tracking-wider text-slate-500">Patient Reference (Dummy)</dt>
                            <dd class="mt-1 text-base font-bold text-slate-800 font-mono" id="detail-patient-reference">{{ $case->patient_reference }}</dd>
                        </div>

                        <!-- Operating Surgeon -->
                        <div class="bg-slate-50/70 p-4 rounded-lg border border-slate-100">
                            <dt class="text-xs font-semibold uppercase tracking-wider text-slate-500">Operating Surgeon</dt>
                            <dd class="mt-1 text-base font-bold text-slate-900" id="detail-surgeon-name">{{ $case->surgeon_name }}</dd>
                        </div>

                        <!-- Implant Type -->
                        <div class="bg-slate-50/70 p-4 rounded-lg border border-slate-100 sm:col-span-2">
                            <dt class="text-xs font-semibold uppercase tracking-wider text-slate-500">Implant System / Specification</dt>
                            <dd class="mt-1 text-base font-medium text-slate-900" id="detail-implant-type">{{ $case->implant_type }}</dd>
                        </div>

                        <!-- Surgery Date -->
                        <div class="bg-slate-50/70 p-4 rounded-lg border border-slate-100">
                            <dt class="text-xs font-semibold uppercase tracking-wider text-slate-500">Surgery Date</dt>
                            <dd class="mt-1 text-base font-semibold text-slate-800" id="detail-surgery-date">
                                {{ $case->surgery_date ? $case->surgery_date->format('l, F j, Y') : 'Not Scheduled' }}
                            </dd>
                        </div>

                    </dl>
                </div>

                <!-- Attribution and Audit Trail -->
                <div class="bg-slate-50 border-t border-slate-200 px-6 py-4">
                    <h4 class="text-xs font-semibold uppercase tracking-wider text-slate-500 mb-3">Audit & Provenance</h4>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs text-slate-600">
                        <div>
                            <span class="block text-slate-400 font-medium">Created By:</span>
                            <span class="font-semibold text-slate-800" id="detail-creator-name">
                                {{ $case->creator ? $case->creator->name : 'System' }}
                            </span>
                            @if($case->creator)
                                <span class="text-slate-500">({{ $case->creator->role }})</span>
                            @endif
                        </div>
                        <div>
                            <span class="block text-slate-400 font-medium">Created At:</span>
                            <span class="font-mono text-slate-700" id="detail-created-at">
                                {{ $case->created_at ? $case->created_at->format('Y-m-d H:i:s T') : '—' }}
                            </span>
                        </div>
                        <div>
                            <span class="block text-slate-400 font-medium">Last Updated:</span>
                            <span class="font-mono text-slate-700" id="detail-updated-at">
                                {{ $case->updated_at ? $case->updated_at->format('Y-m-d H:i:s T') : '—' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Role Access Notice -->
            @if(Auth::user()->isViewer())
                <div class="bg-slate-50 border border-slate-200 rounded-xl p-4 text-xs text-slate-500 flex items-center gap-2">
                    <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>You are logged in as a <strong>Viewer</strong>. You have read-only access to case records. Edit, status modifications, and deletion actions are restricted to Administrators.</span>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
