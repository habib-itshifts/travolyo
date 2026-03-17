<?php

namespace Modules\Customer\Http\Controllers;

use App\Enums\VendorStatusEnum;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('customer::index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('customer::create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request) {}

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        return view('customer::show');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        return view('customer::edit');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id) {}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id) {}

    public function requestVendor(): RedirectResponse
    {
        $user = auth()->user();

        if ($user->vendor_status !== null) {
            return back();
        }

        $user->update(['vendor_status' => VendorStatusEnum::Pending]);

        return back()->with('success', 'Your vendor account request has been sent to admin for approval.');
    }
}
