<?php

namespace Modules\Admin\Http\Controllers;

use App\Enums\UserType;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomerController extends Controller
{
    public function index(Request $request): View
    {
        $customers = User::where('user_type', UserType::Customer)
            ->when($request->filled('search'), function ($q) use ($request) {
                $q->where(function ($q) use ($request) {
                    $q->where('name', 'like', "%{$request->search}%")
                      ->orWhere('email', 'like', "%{$request->search}%")
                      ->orWhere('phone', 'like', "%{$request->search}%");
                });
            })
            ->withCount('bookingsAsCustomer')
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin::customers.index', [
            'customers' => $customers,
            'total'     => User::where('user_type', UserType::Customer)->count(),
        ]);
    }

    public function show(User $customer): View
    {
        abort_unless($customer->user_type === UserType::Customer, 404);

        $customer->loadCount('bookingsAsCustomer');

        return view('admin::customers.show', compact('customer'));
    }

    public function edit(User $customer): View
    {
        abort_unless($customer->user_type === UserType::Customer, 404);

        return view('admin::customers.edit', compact('customer'));
    }

    public function update(Request $request, User $customer): RedirectResponse
    {
        abort_unless($customer->user_type === UserType::Customer, 404);

        $validated = $request->validate([
            'first_name'    => ['required', 'string', 'max:100'],
            'last_name'     => ['nullable', 'string', 'max:100'],
            'email'         => ['required', 'email', 'max:191', 'unique:users,email,' . $customer->id],
            'phone'         => ['nullable', 'string', 'max:30'],
            'address_line_1'=> ['nullable', 'string', 'max:255'],
            'address_line_2'=> ['nullable', 'string', 'max:255'],
            'city'          => ['nullable', 'string', 'max:100'],
            'state'         => ['nullable', 'string', 'max:100'],
            'country'       => ['nullable', 'string', 'max:100'],
            'zip_code'      => ['nullable', 'string', 'max:20'],
        ]);

        $customer->update(array_merge($validated, [
            'name' => trim(($validated['first_name'] ?? '') . ' ' . ($validated['last_name'] ?? '')),
        ]));

        return back()->with('success', 'Customer updated successfully.');
    }
}
