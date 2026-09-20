@extends('layouts.dashboard')

@section('content')
<div class="max-w-2xl mx-auto bg-white p-8 rounded-lg shadow-sm border border-gray-200">
    @php
    $advisoryGrade = old('grade_level', $enrollment->grade_level ?? $user->advisory_grade_level ?? null);
    $advisorySection = old('section', $enrollment->section ?? $user->advisory_section ?? null);
    $advisoryLabel = $advisoryGrade === null || $advisorySection === null
    ? 'Not assigned'
    : 'Section (' . $advisorySection . ') - Grade (' . $advisoryGrade . ')';
    @endphp
    <div class="mb-6 flex items-start justify-between gap-4">
        <h1 class="text-2xl font-bold">{{ isset($student) ? 'Edit Advisory Student' : 'Add Advisory Student' }}</h1>
        <strong class="shrink-0 text-right text-sm text-blue-700">{{ $advisoryLabel }}</strong>
    </div>

    @if ($errors->any())
    <div class="mb-4 bg-red-50 border border-red-200 text-red-600 p-4 rounded text-sm">
        <ul>
            @foreach ($errors->all() as $error)
            <li>• {{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form id="student-form" action="{{ isset($student) ? route('encoder.students.update', $student) : route('encoder.students.store') }}" method="POST" class="space-y-4">
        @csrf
        @if(isset($student)) @method('PUT') @endif

        <div>
            <label class="block text-sm font-semibold mb-1">LRN / Student Number <span class="text-red-500">*</span></label>
            <input id="student-lrn" type="text" name="lrn" value="{{ old('lrn', $student->lrn ?? '') }}" required inputmode="numeric" pattern="[0-9]+" autocomplete="off" class="w-full border rounded p-2 text-sm @error('lrn') border-red-500 @enderror" placeholder="e.g. 136542100012" aria-describedby="student-lrn-error">
            <p id="student-lrn-error" class="mt-1 hidden text-sm text-red-600" role="alert">LRN must contain numbers only. Remove the other characters before submitting.</p>
            @error('lrn')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-semibold mb-1">Last Name <span class="text-red-500">*</span></label>
                <input type="text" name="last_name" value="{{ old('last_name', $student->last_name ?? '') }}" required maxlength="255" pattern="[A-Za-zÀ-ÿ .'-]+" class="w-full border rounded p-2 text-sm" placeholder="e.g. Dela Cruz">
            </div>
            <div>
                <label class="block text-sm font-semibold mb-1">First Name <span class="text-red-500">*</span></label>
                <input type="text" name="first_name" value="{{ old('first_name', $student->first_name ?? '') }}" required maxlength="255" pattern="[A-Za-zÀ-ÿ .'-]+" class="w-full border rounded p-2 text-sm" placeholder="e.g. Juan">
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-semibold mb-1">Name Extension (Optional)</label>
                <input type="text" name="name_extension" value="{{ old('name_extension', $student->name_extension ?? '') }}" maxlength="50" pattern="[A-Za-zÀ-ÿ .'-]+" placeholder="e.g. Jr., III" class="w-full border rounded p-2 text-sm">
            </div>
            <div>
                <label class="block text-sm font-semibold mb-1">Middle Name (Optional)</label>
                <input type="text" name="middle_name" value="{{ old('middle_name', $student->middle_name ?? '') }}" maxlength="255" pattern="[A-Za-zÀ-ÿ .'-]+" placeholder="e.g. Santos" class="w-full border rounded p-2 text-sm">
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-semibold mb-1">Birthdate <span class="text-red-500">*</span></label>
                <input type="date" name="birth_date" value="{{ old('birth_date', isset($student) ? $student->birth_date?->format('Y-m-d') : '') }}" required class="w-full border rounded p-2 text-sm">
            </div>
            <div>
                <label class="block text-sm font-semibold mb-1">Sex <span class="text-red-500">*</span></label>
                <select name="sex" required class="w-full border rounded p-2 text-sm">
                    <option value="">Select Sex</option>
                    <option value="Male" {{ old('sex', $student->sex ?? '') == 'Male' ? 'selected' : '' }}>Male</option>
                    <option value="Female" {{ old('sex', $student->sex ?? '') == 'Female' ? 'selected' : '' }}>Female</option>
                </select>
            </div>
        </div>

        @if(isset($student))
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-semibold mb-1">Grade Level <span class="text-red-500">*</span></label>
                <select name="grade_level" required class="w-full border rounded p-2 text-sm">
                    <option value="" disabled {{ old('grade_level', $enrollment->grade_level ?? '') === '' ? 'selected' : '' }}>Select Grade Level</option>
                    @foreach([0, 1, 2, 3, 4, 5, 6, 7] as $grade)
                    <option value="{{ $grade }}" {{ (string) old('grade_level', $enrollment->grade_level ?? '') === (string) $grade ? 'selected' : '' }}>{{ $grade === 0 ? 'Kinder' : ($grade === 7 ? 'SPED' : $grade) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-semibold mb-1">Section <span class="text-red-500">*</span></label>
                <input type="text" name="section" value="{{ old('section', $enrollment->section ?? '') }}" required maxlength="100" pattern="[A-Za-zÀ-ÿ0-9][A-Za-zÀ-ÿ0-9 .'-]*" class="w-full border rounded p-2 text-sm" placeholder="e.g. Mabini">
            </div>
        </div>
        @else
        <input type="hidden" name="grade_level" value="{{ $advisoryGrade }}">
        <input type="hidden" name="section" value="{{ $advisorySection }}">
        @endif

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-semibold mb-1">Weight (kg) <span class="text-red-500">*</span></label>
                <input id="student-weight" type="text" inputmode="decimal" name="weight" value="{{ old('weight', $measurement->weight ?? '') }}" required maxlength="6" data-min="0.1" data-max="500" placeholder="e.g. 18.5" class="w-full border rounded p-2 text-sm @error('weight') border-red-500 @enderror" aria-describedby="student-weight-error">
                <p id="student-weight-error" class="mt-1 hidden text-sm text-red-600" role="alert"></p>
                @error('weight')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-semibold mb-1">Height (cm) <span class="text-red-500">*</span></label>
                <input id="student-height" type="text" inputmode="decimal" name="height" value="{{ old('height', $measurement->height ?? '') }}" required maxlength="6" data-min="0.1" data-max="300" placeholder="e.g. 115" class="w-full border rounded p-2 text-sm @error('height') border-red-500 @enderror" aria-describedby="student-height-error">
                <p id="student-height-error" class="mt-1 hidden text-sm text-red-600" role="alert"></p>
                @error('height')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-semibold mb-1">Guardian's Name <span class="text-red-500">*</span></label>
                <input type="text" name="guardian_name" value="{{ old('guardian_name', $student->guardian_name ?? '') }}" required maxlength="255" pattern="[A-Za-zÀ-ÿ .'-]+" class="w-full border rounded p-2 text-sm" placeholder="e.g. Maria Dela Cruz">
            </div>
            <div>
                <label class="block text-sm font-semibold mb-1">Guardian's Phone Number <span class="text-red-500">*</span></label>
                <input id="guardian-contact" type="tel" name="guardian_contact" value="{{ old('guardian_contact', $student->guardian_contact ?? '') }}" required maxlength="13" pattern="\+?[0-9]{11,12}" inputmode="tel" class="w-full border rounded p-2 text-sm @error('guardian_contact') border-red-500 @enderror" placeholder="e.g. 09171234567" aria-describedby="guardian-contact-error">
                <p id="guardian-contact-error" class="mt-1 hidden text-sm text-red-600" role="alert"></p>
                @error('guardian_contact')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
        </div>

        <div>
            <label class="block text-sm font-semibold mb-1">Guardian's Email (Optional - for daily meal/nutrition updates)</label>
            <input type="email" name="guardian_email" value="{{ old('guardian_email', $student->guardian_email ?? '') }}" maxlength="255" placeholder="guardian@example.com" class="w-full border rounded p-2 text-sm">
        </div>

        <div>
            <label class="block text-sm font-semibold mb-1">Complete Address <span class="text-red-500">*</span></label>
            <textarea name="address" required maxlength="500" rows="2" class="w-full border rounded p-2 text-sm" placeholder="House number, street, barangay, municipality">{{ old('address', $student->address ?? '') }}</textarea>
        </div>

        <div class="flex justify-end gap-3 pt-4">
            <a href="{{ route('encoder.students.index') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded text-sm hover:bg-gray-300">Cancel</a>
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded text-sm hover:bg-blue-700">{{ isset($student) ? 'Update Student' : 'Save Student' }}</button>
        </div>
    </form>
</div>
<script>
    (() => {
        const form = document.getElementById('student-form');
        const fields = [
            {
                input: document.getElementById('student-lrn'),
                error: document.getElementById('student-lrn-error'),
                validate: (value) => /^[0-9]+$/.test(value) ? '' : 'LRN must contain numbers only. Remove the other characters before submitting.'
            },
            {
                input: document.getElementById('student-weight'),
                error: document.getElementById('student-weight-error'),
                validate: (value, input) => validateMeasurement(value, input, 'Weight', 'kg')
            },
            {
                input: document.getElementById('student-height'),
                error: document.getElementById('student-height-error'),
                validate: (value, input) => validateMeasurement(value, input, 'Height', 'cm')
            },
            {
                input: document.getElementById('guardian-contact'),
                error: document.getElementById('guardian-contact-error'),
                validate: (value) => /^\+?[0-9]{11,12}$/.test(value) ? '' : 'Guardian phone number must contain 11 or 12 digits. Use an optional leading + only.'
            }
        ];

        if (!form) return;

        function validateMeasurement(value, input, label, unit) {
            if (!value) return `${label} is required.`;
            if (!/^\d+(\.\d+)?$/.test(value)) return `${label} must be a number in ${unit}.`;
            const numericValue = Number(value);
            if (numericValue < Number(input.dataset.min) || numericValue > Number(input.dataset.max)) {
                return `${label} must be between ${input.dataset.min} and ${input.dataset.max} ${unit}.`;
            }
            return '';
        }

        function updateField(field) {
            if (!field.input || !field.error) return true;
            const message = field.validate(field.input.value.trim(), field.input);
            const valid = message === '';
            field.error.textContent = message;
            field.error.classList.toggle('hidden', valid);
            field.input.classList.toggle('border-red-500', !valid);
            field.input.classList.toggle('ring-1', !valid);
            field.input.classList.toggle('ring-red-500', !valid);
            field.input.setAttribute('aria-invalid', valid ? 'false' : 'true');
            return valid;
        }

        fields.forEach((field) => {
            if (!field.input) return;
            field.input.addEventListener('input', () => updateField(field));
            field.input.addEventListener('blur', () => updateField(field));
        });

        form.addEventListener('submit', (event) => {
            const invalidField = fields.find((field) => !updateField(field));
            if (invalidField) {
                event.preventDefault();
                invalidField.input.focus();
            }
        });
    })();
</script>
@endsection