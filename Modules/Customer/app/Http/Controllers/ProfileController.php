<?php

namespace Modules\Customer\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public function edit()
    {
        return view('customer::profile.edit', ['user' => auth()->user()]);
    }

    public function update(Request $request): RedirectResponse
    {
        $user = auth()->user();

        $validated = $request->validate([
            'first_name'         => ['required', 'string', 'max:100'],
            'last_name'          => ['nullable', 'string', 'max:100'],
            'username'           => ['nullable', 'string', 'max:50', 'unique:users,username,' . $user->id],
            'gender'             => ['nullable', 'in:male,female,other'],
            'birthday'           => ['nullable', 'date', 'before:today'],
            'nationality'        => ['nullable', 'string', 'max:100'],
            'phone'              => ['nullable', 'string', 'max:30'],
            'phone_country_code' => ['nullable', 'string', 'max:10'],
            'whatsapp_number'    => ['nullable', 'string', 'max:30'],
            'address_line_1'     => ['nullable', 'string', 'max:255'],
            'address_line_2'     => ['nullable', 'string', 'max:255'],
            'city'               => ['nullable', 'string', 'max:100'],
            'state'              => ['nullable', 'string', 'max:100'],
            'country'            => ['nullable', 'string', 'max:100'],
            'zip_code'           => ['nullable', 'string', 'max:20'],
        ]);

        $validated['name'] = trim(($validated['first_name'] ?? '') . ' ' . ($validated['last_name'] ?? ''));

        $user->update($validated);

        return back()->with('success', 'Profile updated successfully.');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password'         => ['required', 'confirmed', Password::defaults()],
        ]);

        auth()->user()->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->with('success', 'Password changed successfully.');
    }
}
