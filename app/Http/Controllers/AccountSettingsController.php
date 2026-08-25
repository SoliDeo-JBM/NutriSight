<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AccountSettingsController extends Controller
{
    public function edit(Request $request)
    {
        return view('account.settings', [
            'user' => $request->user(),
        ]);
    }

    public function update(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'deped_id' => ['nullable', 'string', 'max:255', 'unique:users,deped_id,' . $user->id],
            'sex' => ['nullable', 'in:Male,Female'],
            'birthdate' => ['nullable', 'date'],
            'position' => ['nullable', 'string', 'max:255'],
            'advisory_grade_level' => ['nullable', 'string', 'max:255'],
            'advisory_section' => ['nullable', 'string', 'max:255'],
        ]);

        $user->update($validated);

        \App\Services\AuditLogger::log('Updated', 'Account Settings', 'Updated account profile information');

        return back()->with('success', 'Account information updated successfully.');
    }

    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        \App\Services\AuditLogger::log('Updated', 'Security', 'Updated account password');

        return back()->with('success', 'Password updated successfully.');
    }
}
