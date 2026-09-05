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
                <label class="block text-sm font-semibold mb-1">LRN / Student Number</label>
                <input type="text" name="student_number" value="{{ old('student_number') }}" required class="w-full border rounded p-2 text-sm">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold mb-1">Last Name</label>
                    <input type="text" name="last_name" value="{{ old('last_name') }}" required class="w-full border rounded p-2 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-1">First Name</label>
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
                    <label class="block text-sm font-semibold mb-1">Birthdate</label>
                    <input type="date" name="birth_date" value="{{ old('birth_date') }}" required class="w-full border rounded p-2 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-1">Sex</label>
                    <select name="gender" required class="w-full border rounded p-2 text-sm">
                        <option value="">Select Sex</option>
                        <option value="Male" {{ old('gender') == 'Male' ? 'selected' : '' }}>Male</option>
                        <option value="Female" {{ old('gender') == 'Female' ? 'selected' : '' }}>Female</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold mb-1">Grade Level</label>
                    <input type="text" name="grade_level" value="Grade 1" required class="w-full border rounded p-2 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-1">Section</label>
                    <input type="text" name="section" value="Diamond" required class="w-full border rounded p-2 text-sm">
                    <input type="hidden" name="section_id" value="1">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold mb-1">Weight (kg)</label>
                    <input type="number" step="0.1" name="weight" value="{{ old('weight') }}" required placeholder="e.g. 18.5" class="w-full border rounded p-2 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-1">Height (cm)</label>
                    <input type="number" step="0.1" name="height" value="{{ old('height') }}" required placeholder="e.g. 115" class="w-full border rounded p-2 text-sm">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold mb-1">Guardian's Name</label>
                    <input type="text" name="guardian_name" value="{{ old('guardian_name') }}" required class="w-full border rounded p-2 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-1">Guardian's Phone Number</label>
                    <input type="text" name="guardian_contact" value="{{ old('guardian_contact') }}" required class="w-full border rounded p-2 text-sm">
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold mb-1">Guardian's Email (Optional - for daily meal/nutrition updates)</label>
                <input type="email" name="guardian_email" value="{{ old('guardian_email') }}" placeholder="guardian@example.com" class="w-full border rounded p-2 text-sm">
            </div>

            <div>
                <label class="block text-sm font-semibold mb-1">Complete Address</label>
                <textarea name="address" required rows="2" class="w-full border rounded p-2 text-sm">{{ old('address') }}</textarea>
            </div>

            <div class="flex justify-end gap-3 pt-4">
                <a href="{{ route('encoder.students.index') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded text-sm hover:bg-gray-300">Cancel</a>
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded text-sm hover:bg-blue-700">Save Student</button>
            </div>
        </form>
    </div>
@endsection
