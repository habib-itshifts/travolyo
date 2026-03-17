<?php

namespace Modules\Admin\Http\Controllers;

use App\Enums\UserType;
use App\Enums\VendorStatusEnum;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VendorController extends Controller
{
    public function index(Request $request): View
    {
        $vendors = User::where('user_type', UserType::Vendor)
            ->when($request->filled('search'), function ($q) use ($request) {
                $q->where(function ($q) use ($request) {
                    $q->where('name', 'like', "%{$request->search}%")
                      ->orWhere('email', 'like', "%{$request->search}%")
                      ->orWhere('business_name', 'like', "%{$request->search}%");
                });
            })
            ->when($request->filled('status'), fn ($q) => $q->where('vendor_status', $request->status))
            ->withCount('bookingsAsVendor')
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin::vendors.index', [
            'vendors' => $vendors,
            'counts'  => [
                'all'            => User::where('user_type', UserType::Vendor)->count(),
                'verified'       => User::where('user_type', UserType::Vendor)->where('vendor_status', VendorStatusEnum::Verified)->count(),
                'pending'        => User::where('user_type', UserType::Vendor)->where('vendor_status', VendorStatusEnum::Pending)->count(),
            ],
        ]);
    }

    public function show(User $vendor): View
    {
        abort_unless($vendor->user_type === UserType::Vendor, 404);

        $vendor->loadCount('bookingsAsVendor');

        return view('admin::vendors.show', compact('vendor'));
    }

    public function edit(User $vendor): View
    {
        abort_unless($vendor->user_type === UserType::Vendor, 404);

        return view('admin::vendors.edit', compact('vendor'));
    }

    public function update(Request $request, User $vendor): RedirectResponse
    {
        abort_unless($vendor->user_type === UserType::Vendor, 404);

        $validated = $request->validate([
            'first_name'    => ['required', 'string', 'max:100'],
            'last_name'     => ['nullable', 'string', 'max:100'],
            'email'         => ['required', 'email', 'max:191', 'unique:users,email,' . $vendor->id],
            'phone'         => ['nullable', 'string', 'max:30'],
            'business_name' => ['nullable', 'string', 'max:191'],
            'tax_number'    => ['nullable', 'string', 'max:100'],
            'address_line_1'=> ['nullable', 'string', 'max:255'],
            'address_line_2'=> ['nullable', 'string', 'max:255'],
            'city'          => ['nullable', 'string', 'max:100'],
            'state'         => ['nullable', 'string', 'max:100'],
            'country'       => ['nullable', 'string', 'max:100'],
            'zip_code'      => ['nullable', 'string', 'max:20'],
            'vendor_status'             => ['nullable', 'string'],
            'vendor_commission_type'    => ['nullable', 'in:percent,fixed'],
            'vendor_commission_amount'  => ['nullable', 'numeric', 'min:0'],
        ]);

        $vendor->update(array_merge($validated, [
            'name' => trim(($validated['first_name'] ?? '') . ' ' . ($validated['last_name'] ?? '')),
        ]));

        return back()->with('success', 'Vendor updated successfully.');
    }
}
