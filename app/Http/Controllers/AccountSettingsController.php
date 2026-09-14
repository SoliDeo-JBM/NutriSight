<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use App\Models\User;

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
            'first_name' => ['required_without:name', 'nullable', 'string', 'max:255'],
            'middle_name' => ['nullable', 'string', 'max:255'],
            'last_name' => ['required_without:name', 'nullable', 'string', 'max:255'],
            'name_extension' => ['nullable', 'string', 'max:50'],
            'name' => ['nullable', 'string', 'max:255'],
            'sex' => ['nullable', 'in:Male,Female'],
            'birthdate' => ['nullable', 'date'],
        ]);

        if ($user->role === 'super_admin') {
            $validated = array_merge($validated, $request->validate([
                    'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $user->id],
                'deped_id' => ['nullable', 'string', 'max:255', 'unique:users,deped_id,' . $user->id],
                'position' => ['nullable', 'string', 'max:255'],
            ]));
        }

        $user->update([
            'name' => User::composeName($validated['first_name'] ?? null, $validated['middle_name'] ?? null, $validated['last_name'] ?? null, $validated['name_extension'] ?? null) ?: $validated['name'],
            'first_name' => $validated['first_name'] ?? null,
            'middle_name' => $validated['middle_name'] ?? null,
            'last_name' => $validated['last_name'] ?? null,
            'name_extension' => $validated['name_extension'] ?? null,
            'sex' => $validated['sex'] ?? null,
            'birthdate' => $validated['birthdate'] ?? null,
            ...($user->role === 'super_admin' ? ['email' => $validated['email']] : []),
            ...($user->role === 'super_admin' ? [
                'deped_id' => $validated['deped_id'] ?? null,
                'position' => $validated['position'] ?? null,
            ] : []),
        ]);

        if ($user->role === 'super_admin') {
            $assignment = $user->currentSchoolYearUserRecord() ?? $user->syncSchoolYearUserRecord();
            if ($assignment) {
                $assignment->update([
                    'deped_id' => $validated['deped_id'] ?? null,
                    'position' => $validated['position'] ?? null,
                ]);
            } else {
                $user->update([
                    'deped_id' => $validated['deped_id'] ?? null,
                    'position' => $validated['position'] ?? null,
                ]);
            }
        }

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
