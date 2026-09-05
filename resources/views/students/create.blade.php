@extends('layouts.dashboard')

@section('content')
    <div class="max-w-2xl mx-auto bg-white p-8 rounded-lg shadow-sm border border-gray-200">
        <h1 class="text-2xl font-bold mb-6">Add Advisory Student</h1>

        @if ($errors->any())
            <div class="mb-4 bg-red-50 border border-red-200 text-red-600 p-4 rounded text-sm">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>• {{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('encoder.students.store') }}" method="POST" class="space-y-4">
            @csrf

            <div>
                <label class="block text-sm font-semibold mb-1">LRN / Student Number <span class="text-red-500">*</span></label>
                <input type="text" name="lrn" value="{{ old('lrn') }}" required class="w-full border rounded p-2 text-sm" placeholder="e.g. 136542100012">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold mb-1">Last Name <span class="text-red-500">*</span></label>
                    <input type="text" name="last_name" value="{{ old('last_name') }}" required class="w-full border rounded p-2 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-1">First Name <span class="text-red-500">*</span></label>
                    <input type="text" name="first_name" value="{{ old('first_name') }}" required class="w-full border rounded p-2 text-sm">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold mb-1">Name Extension (Optional)</label>
                    <input type="text" name="name_extension" value="{{ old('name_extension') }}" placeholder="e.g. Jr., III" class="w-full border rounded p-2 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-1">Middle Name (Optional)</label>
                    <input type="text" name="middle_name" value="{{ old('middle_name') }}" class="w-full border rounded p-2 text-sm">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold mb-1">Birthdate <span class="text-red-500">*</span></label>
                    <input type="date" name="birth_date" value="{{ old('birth_date') }}" required class="w-full border rounded p-2 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-1">Sex <span class="text-red-500">*</span></label>
                    <select name="sex" required class="w-full border rounded p-2 text-sm">
                        <option value="">Select Sex</option>
                        <option value="Male" {{ old('sex') == 'Male' ? 'selected' : '' }}>Male</option>
                        <option value="Female" {{ old('sex') == 'Female' ? 'selected' : '' }}>Female</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold mb-1">Grade Level <span class="text-red-500">*</span></label>
                    <select name="grade_level" required class="w-full border rounded p-2 text-sm">
                        <option value="">-- Select Grade Level --</option>
                        <option value="0" {{ old('grade_level') == '0' ? 'selected' : '' }}>Kinder</option>
                        <option value="1" {{ old('grade_level') == '1' ? 'selected' : '' }}>Grade 1</option>
                        <option value="2" {{ old('grade_level') == '2' ? 'selected' : '' }}>Grade 2</option>
                        <option value="3" {{ old('grade_level') == '3' ? 'selected' : '' }}>Grade 3</option>
                        <option value="4" {{ old('grade_level') == '4' ? 'selected' : '' }}>Grade 4</option>
                        <option value="5" {{ old('grade_level') == '5' ? 'selected' : '' }}>Grade 5</option>
                        <option value="6" {{ old('grade_level') == '6' ? 'selected' : '' }}>Grade 6</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-1">Section <span class="text-red-500">*</span></label>
                    <input type="text" name="section" value="{{ old('section', auth()->user()->advisory_section) }}" required class="w-full border rounded p-2 text-sm" placeholder="e.g. Diamond">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold mb-1">Weight (kg) <span class="text-red-500">*</span></label>
                    <input type="number" step="0.1" name="weight" value="{{ old('weight') }}" required placeholder="e.g. 18.5" class="w-full border rounded p-2 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-1">Height (cm) <span class="text-red-500">*</span></label>
                    <input type="number" step="0.1" name="height" value="{{ old('height') }}" required placeholder="e.g. 115" class="w-full border rounded p-2 text-sm">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold mb-1">Guardian's Name <span class="text-red-500">*</span></label>
                    <input type="text" name="guardian_name" value="{{ old('guardian_name') }}" required class="w-full border rounded p-2 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-1">Guardian's Phone Number <span class="text-red-500">*</span></label>
                    <input type="text" name="guardian_contact" value="{{ old('guardian_contact') }}" required class="w-full border rounded p-2 text-sm">
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold mb-1">Guardian's Email (Optional - for daily meal/nutrition updates)</label>
                <input type="email" name="guardian_email" value="{{ old('guardian_email') }}" placeholder="guardian@example.com" class="w-full border rounded p-2 text-sm">
            </div>

            <div>
                <label class="block text-sm font-semibold mb-1">Complete Address <span class="text-red-500">*</span></label>
                <textarea name="address" required rows="2" class="w-full border rounded p-2 text-sm">{{ old('address') }}</textarea>
            </div>

            <div class="flex justify-end gap-3 pt-4">
                <a href="{{ route('encoder.students.index') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded text-sm hover:bg-gray-300">Cancel</a>
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded text-sm hover:bg-blue-700">Save Student</button>
            </div>
        </form>
    </div>
@endsection
