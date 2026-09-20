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
            <input id="student-lrn" type="text" name="lrn" value="{{ old('lrn', $student->lrn ?? '') }}" required inputmode="numeric" pattern="[0-9]+" autocomplete="off" class="w-full border rounded p-2 text-sm" placeholder="e.g. 136542100012" aria-describedby="student-lrn-error">
            <p id="student-lrn-error" class="mt-1 hidden text-sm text-red-600" role="alert">LRN must contain numbers only. Remove the other characters before submitting.</p>
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
                <input type="number" step="0.1" min="0.1" max="500" name="weight" value="{{ old('weight', $measurement->weight ?? '') }}" required placeholder="e.g. 18.5" class="w-full border rounded p-2 text-sm">
            </div>
            <div>
                <label class="block text-sm font-semibold mb-1">Height (cm) <span class="text-red-500">*</span></label>
                <input type="number" step="0.1" min="0.1" max="300" name="height" value="{{ old('height', $measurement->height ?? '') }}" required placeholder="e.g. 115" class="w-full border rounded p-2 text-sm">
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-semibold mb-1">Guardian's Name <span class="text-red-500">*</span></label>
                <input type="text" name="guardian_name" value="{{ old('guardian_name', $student->guardian_name ?? '') }}" required maxlength="255" pattern="[A-Za-zÀ-ÿ .'-]+" class="w-full border rounded p-2 text-sm" placeholder="e.g. Maria Dela Cruz">
            </div>
            <div>
                <label class="block text-sm font-semibold mb-1">Guardian's Phone Number <span class="text-red-500">*</span></label>
                <input type="tel" name="guardian_contact" value="{{ old('guardian_contact', $student->guardian_contact ?? '') }}" required maxlength="20" pattern="\+?[0-9][0-9 -]{6,14}" class="w-full border rounded p-2 text-sm" placeholder="e.g. 09171234567">
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
        const lrnInput = document.getElementById('student-lrn');
        const lrnError = document.getElementById('student-lrn-error');
        let invalidLrnAttempt = false;

        if (!form || !lrnInput || !lrnError) return;

        const showLrnError = () => {
            lrnError.classList.remove('hidden');
            lrnInput.classList.add('border-red-500', 'ring-1', 'ring-red-500');
        };

        const clearLrnError = () => {
            lrnError.classList.add('hidden');
            lrnInput.classList.remove('border-red-500', 'ring-1', 'ring-red-500');
        };

        lrnInput.addEventListener('keydown', (event) => {
            if (event.ctrlKey || event.metaKey || event.altKey || event.key.length !== 1) return;
            if (!/[0-9]/.test(event.key)) {
                event.preventDefault();
                invalidLrnAttempt = true;
                showLrnError();
            }
        });

        lrnInput.addEventListener('input', () => {
            if (/^[0-9]*$/.test(lrnInput.value)) {
                if (!invalidLrnAttempt) clearLrnError();
                return;
            }

            invalidLrnAttempt = true;
            lrnInput.value = lrnInput.value.replace(/[^0-9]/g, '');
            showLrnError();
        });

        form.addEventListener('submit', (event) => {
            if (invalidLrnAttempt || !/^[0-9]+$/.test(lrnInput.value)) {
                event.preventDefault();
                showLrnError();
                lrnInput.focus();
            }
        });
    })();
</script>
@endsection