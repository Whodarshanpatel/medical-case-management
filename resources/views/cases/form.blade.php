<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <div class="flex items-center gap-2 text-xs text-slate-500 mb-1">
                    <a href="{{ route('cases.index') }}" class="hover:text-teal-600">Cases</a>
                    <span>&rsaquo;</span>
                    <span>{{ $isEdit ? 'Edit ' . $case->case_number : 'New Case' }}</span>
                </div>
                <h2 class="font-bold text-2xl text-slate-800 leading-tight">
                    {{ $isEdit ? __('Edit Medical Case: ') . $case->case_number : __('Create New Medical Case') }}
                </h2>
            </div>
            <div>
                <a href="{{ $isEdit ? route('cases.show', $case) : route('cases.index') }}" class="inline-flex items-center px-3.5 py-2 bg-white border border-slate-300 rounded-lg text-sm font-medium text-slate-700 shadow-sm hover:bg-slate-50 transition">
                    &larr; Cancel & Back
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-6 py-5 border-b border-slate-200 bg-slate-50/50">
                    <h3 class="font-semibold text-slate-800 text-base">
                        {{ $isEdit ? 'Case Information Update' : 'New Case Requisition Details' }}
                    </h3>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Please fill in the verified clinical, surgical, and implant details. All fields except Surgery Date are required.
                    </p>
                </div>

                <form method="POST" action="{{ $isEdit ? route('cases.update', $case) : route('cases.store') }}" class="p-6 space-y-6">
                    @csrf
                    @if($isEdit)
                        @method('PUT')
                    @endif

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                        <!-- Case Number -->
                        <div>
                            <label for="case_number" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1">
                                Case Number <span class="text-rose-500">*</span>
                            </label>
                            <input
                                type="text"
                                name="case_number"
                                id="case_number"
                                value="{{ old('case_number', $case->case_number) }}"
                                placeholder="e.g. CAS-20451"
                                class="w-full rounded-lg border-slate-300 text-sm focus:ring-teal-500 focus:border-teal-500 @error('case_number') border-rose-300 bg-rose-50/30 @enderror"
                                required
                            />
                            @error('case_number')
                                <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-xs text-slate-400">Must be unique across all clinical records.</p>
                        </div>

                        <!-- Patient Reference (Dummy) -->
                        <div>
                            <label for="patient_reference" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1">
                                Patient Reference <span class="text-rose-500">*</span>
                            </label>
                            <input
                                type="text"
                                name="patient_reference"
                                id="patient_reference"
                                value="{{ old('patient_reference', $case->patient_reference) }}"
                                placeholder="e.g. PT-8821"
                                class="w-full rounded-lg border-slate-300 text-sm focus:ring-teal-500 focus:border-teal-500 @error('patient_reference') border-rose-300 bg-rose-50/30 @enderror"
                                required
                            />
                            @error('patient_reference')
                                <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-xs text-slate-400">De-identified dummy patient code only. No real PII.</p>
                        </div>

                        <!-- Surgeon Name -->
                        <div>
                            <label for="surgeon_name" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1">
                                Surgeon Name <span class="text-rose-500">*</span>
                            </label>
                            <input
                                type="text"
                                name="surgeon_name"
                                id="surgeon_name"
                                value="{{ old('surgeon_name', $case->surgeon_name) }}"
                                placeholder="e.g. Dr. Arthur Bell"
                                class="w-full rounded-lg border-slate-300 text-sm focus:ring-teal-500 focus:border-teal-500 @error('surgeon_name') border-rose-300 bg-rose-50/30 @enderror"
                                required
                            />
                            @error('surgeon_name')
                                <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Implant Type -->
                        <div>
                            <label for="implant_type" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1">
                                Implant Type <span class="text-rose-500">*</span>
                            </label>
                            <input
                                type="text"
                                name="implant_type"
                                id="implant_type"
                                value="{{ old('implant_type', $case->implant_type) }}"
                                placeholder="e.g. Titanium Total Knee System"
                                class="w-full rounded-lg border-slate-300 text-sm focus:ring-teal-500 focus:border-teal-500 @error('implant_type') border-rose-300 bg-rose-50/30 @enderror"
                                required
                            />
                            @error('implant_type')
                                <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Status Dropdown -->
                        <div>
                            <label for="status" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1">
                                Case Status <span class="text-rose-500">*</span>
                            </label>
                            <select
                                name="status"
                                id="status"
                                class="w-full rounded-lg border-slate-300 text-sm focus:ring-teal-500 focus:border-teal-500 @error('status') border-rose-300 bg-rose-50/30 @enderror"
                                required
                            >
                                <option value="" disabled {{ !old('status', $case->status) ? 'selected' : '' }}>Select status...</option>
                                @foreach($statuses as $st)
                                    <option value="{{ $st }}" {{ old('status', $case->status ?? 'Planning') === $st ? 'selected' : '' }}>
                                        {{ $st }}
                                    </option>
                                @endforeach
                            </select>
                            @error('status')
                                <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Priority Dropdown -->
                        <div>
                            <label for="priority" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1">
                                Priority Level <span class="text-rose-500">*</span>
                            </label>
                            <select
                                name="priority"
                                id="priority"
                                class="w-full rounded-lg border-slate-300 text-sm focus:ring-teal-500 focus:border-teal-500 @error('priority') border-rose-300 bg-rose-50/30 @enderror"
                                required
                            >
                                <option value="" disabled {{ !old('priority', $case->priority) ? 'selected' : '' }}>Select priority...</option>
                                @foreach($priorities as $pr)
                                    <option value="{{ $pr }}" {{ old('priority', $case->priority ?? 'Medium') === $pr ? 'selected' : '' }}>
                                        {{ $pr }}
                                    </option>
                                @endforeach
                            </select>
                            @error('priority')
                                <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Surgery Date -->
                        <div class="md:col-span-2">
                            <label for="surgery_date" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1">
                                Surgery Date <span class="text-slate-400 font-normal">(Optional)</span>
                            </label>
                            <input
                                type="date"
                                name="surgery_date"
                                id="surgery_date"
                                value="{{ old('surgery_date', $case->surgery_date ? $case->surgery_date->format('Y-m-d') : '') }}"
                                class="w-full md:w-1/2 rounded-lg border-slate-300 text-sm focus:ring-teal-500 focus:border-teal-500 @error('surgery_date') border-rose-300 bg-rose-50/30 @enderror"
                            />
                            @error('surgery_date')
                                <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                    </div>

                    <!-- Security Note -->
                    <div class="rounded-lg bg-slate-50 border border-slate-200 p-3.5 text-xs text-slate-600 flex items-center gap-2">
                        <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>Creator is automatically attributed to authenticated user <strong class="text-slate-800">{{ Auth::user()->name }}</strong>. Client cannot manipulate creator IDs.</span>
                    </div>

                    <!-- Form Buttons -->
                    <div class="pt-4 border-t border-slate-200 flex items-center justify-end gap-3">
                        <a href="{{ $isEdit ? route('cases.show', $case) : route('cases.index') }}" class="px-4 py-2 border border-slate-300 rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-50 transition">
                            Cancel
                        </a>
                        <button
                            type="submit"
                            id="btn-save-case"
                            class="px-5 py-2 bg-teal-600 hover:bg-teal-700 text-white rounded-lg text-sm font-semibold shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-offset-2 transition"
                        >
                            {{ $isEdit ? 'Save Changes' : 'Create Medical Case' }}
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</x-app-layout>
