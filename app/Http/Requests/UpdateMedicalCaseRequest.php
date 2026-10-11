<?php

namespace App\Http\Requests;

use App\Models\MedicalCase;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateMedicalCaseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $caseParam = $this->route('case') ?? $this->route('id');
        $case = $caseParam instanceof MedicalCase ? $caseParam : MedicalCase::find($caseParam);

        return $this->user() && ($case ? $this->user()->can('update', $case) : $this->user()->isAdmin());
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $caseParam = $this->route('case') ?? $this->route('id');
        $caseId = $caseParam instanceof MedicalCase ? $caseParam->id : $caseParam;

        return [
            'case_number' => [
                'required',
                'string',
                'max:50',
                Rule::unique('cases', 'case_number')->ignore($caseId),
            ],
            'patient_reference' => ['required', 'string', 'max:100'],
            'surgeon_name' => ['required', 'string', 'max:150'],
            'implant_type' => ['required', 'string', 'max:150'],
            'status' => ['required', 'string', Rule::in(MedicalCase::STATUSES)],
            'priority' => ['required', 'string', Rule::in(MedicalCase::PRIORITIES)],
            'surgery_date' => ['nullable', 'date'],
        ];
    }

    /**
     * Custom validation messages.
     */
    public function messages(): array
    {
        return [
            'case_number.required' => 'The Case Number is required.',
            'case_number.unique' => 'The Case Number has already been taken.',
            'patient_reference.required' => 'The Patient Reference is required.',
            'surgeon_name.required' => 'The Surgeon Name is required.',
            'implant_type.required' => 'The Implant Type is required.',
            'status.required' => 'The Status is required.',
            'status.in' => 'Selected status is invalid.',
            'priority.required' => 'The Priority is required.',
            'priority.in' => 'Selected priority is invalid.',
            'surgery_date.date' => 'The Surgery Date must be a valid date.',
        ];
    }
}
