<?php

namespace Modules\Admin\Http\Controllers;

use App\Enums\VendorDocumentStatusEnum;
use Illuminate\Support\Facades\Storage;
use App\Enums\VendorStatusEnum;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Modules\Vendor\Models\VendorDocument;

class VendorRequestController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->get('status', 'pending');

        $requests = User::where('vendor_status', $status)
            ->latest('updated_at')
            ->paginate(20);

        return view('admin::vendor-requests.index', [
            'requests'      => $requests,
            'activeStatus'  => $status,
            'counts'        => [
                'pending'        => User::where('vendor_status', VendorStatusEnum::Pending)->count(),
                'approved'       => User::where('vendor_status', VendorStatusEnum::Approved)->count(),
                'docs_submitted' => User::where('vendor_status', VendorStatusEnum::DocsSubmitted)->count(),
                'rejected'       => User::where('vendor_status', VendorStatusEnum::Rejected)->count(),
                'verified'       => User::where('vendor_status', VendorStatusEnum::Verified)->count(),
            ],
        ]);
    }

    public function approve(User $user): RedirectResponse
    {
        $user->update([
            'vendor_status' => VendorStatusEnum::Approved,
        ]);

        // Assign vendor role so the portal unlocks
        $user->syncRoles(['vendor']);

        return back()->with('success', "Vendor request approved for {$user->name}. They can now upload documents.");
    }

    public function reject(User $user): RedirectResponse
    {
        $user->update([
            'vendor_status' => VendorStatusEnum::Rejected,
        ]);

        return back()->with('success', "Vendor request rejected for {$user->name}.");
    }

    public function documents(User $user)
    {
        $docs = $user->vendorDocuments()->latest()->get();

        return view('admin::vendor-requests.documents', [
            'vendor' => $user,
            'docs'   => $docs,
        ]);
    }

    public function downloadDocument(VendorDocument $document)
    {
        abort_unless(Storage::disk('private')->exists($document->file_path), 404);

        return Storage::disk('private')->download($document->file_path, $document->original_name);
    }

    public function approveDocument(VendorDocument $document): RedirectResponse
    {
        $document->update([
            'status'      => VendorDocumentStatusEnum::Approved,
            'admin_note'  => null,
            'reviewed_by' => auth()->user()?->id,
            'reviewed_at' => now(),
        ]);

        return back()->with('success', 'Document approved.');
    }

    public function rejectDocument(Request $request, VendorDocument $document): RedirectResponse
    {
        $request->validate(['note' => ['nullable', 'string', 'max:500']]);

        $document->update([
            'status'      => VendorDocumentStatusEnum::Rejected,
            'admin_note'  => $request->note,
            'reviewed_by' => auth()->user()?->id,
            'reviewed_at' => now(),
        ]);

        return back()->with('success', 'Document rejected.');
    }

    public function verify(User $user): RedirectResponse
    {
        $user->update(['vendor_status' => VendorStatusEnum::Verified]);

        return back()->with('success', "{$user->name} has been verified as a vendor.");
    }
}
