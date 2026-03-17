<?php

namespace Modules\Vendor\Http\Controllers;

use App\Enums\VendorDocumentTypeEnum;
use App\Enums\VendorStatusEnum;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Modules\Vendor\Models\VendorDocument;

class VendorDocumentController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $docs = $user->vendorDocuments()->get()->keyBy(fn($d) => $d->type->value);

        return view('vendor::documents.index', [
            'types' => VendorDocumentTypeEnum::cases(),
            'docs'  => $docs,
            'user'  => $user,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'type' => ['required', 'string', 'in:' . implode(',', array_column(VendorDocumentTypeEnum::cases(), 'value'))],
            'file' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'], // 5MB
        ]);

        $user = auth()->user();
        $type = VendorDocumentTypeEnum::from($request->type);

        // Delete existing doc of same type before replacing
        $existing = $user->vendorDocuments()->where('type', $type->value)->first();
        if ($existing) {
            if (file_exists(storage_path('app/private/' . $existing->file_path))) {
                unlink(storage_path('app/private/' . $existing->file_path));
            }
            $existing->delete();
        }

        $file      = $request->file('file');
        $path      = $file->store("vendor-docs/{$user->id}", 'private');

        VendorDocument::create([
            'user_id'       => $user->id,
            'type'          => $type,
            'file_path'     => $path,
            'original_name' => $file->getClientOriginalName(),
            'mime_type'     => $file->getMimeType(),
            'file_size'     => $file->getSize(),
            'status'        => 'pending',
        ]);

        return back()->with('success', "{$type->label()} uploaded successfully.");
    }

    public function destroy(VendorDocument $document): RedirectResponse
    {
        abort_unless($document->user_id === auth()->id(), 403);

        if (file_exists(storage_path('app/private/' . $document->file_path))) {
            unlink(storage_path('app/private/' . $document->file_path));
        }

        $document->delete();

        return back()->with('success', 'Document removed.');
    }

    public function submit(): RedirectResponse
    {
        $user     = auth()->user();
        $required = array_filter(VendorDocumentTypeEnum::cases(), fn($t) => $t->isRequired());
        $uploaded = $user->vendorDocuments()->pluck('type')->map(fn($t) => $t->value)->toArray();

        foreach ($required as $type) {
            if (!in_array($type->value, $uploaded)) {
                return back()->with('error', "Please upload your {$type->label()} before submitting.");
            }
        }

        $user->update(['vendor_status' => VendorStatusEnum::DocsSubmitted]);

        return back()->with('success', 'Documents submitted for admin review. You will be notified once verified.');
    }
}
