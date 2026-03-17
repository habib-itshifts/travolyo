<?php

namespace Modules\Activity\Http\Controllers\Vendor;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\View\View;
use Modules\Activity\Http\Requests\Vendor\StoreActivityRequest;
use Modules\Activity\Http\Requests\Vendor\UpdateActivityRequest;
use Modules\Activity\Models\Activity;
use Modules\Activity\Services\ActivityService;

class ActivityController extends Controller
{
    public function __construct(private readonly ActivityService $service) {}

    public function index(Request $request): View
    {
        return view('vendor::activities.index', $this->service->vendorIndex($request, auth()->id()));
    }

    public function create(): View
    {
        return view('vendor::activities.create', $this->service->createData());
    }

    public function store(StoreActivityRequest $request): RedirectResponse
    {
        $activity = $this->service->save($request->validated(), isVendor: true);

        return redirect()->route('vendor.activities.index')
            ->with('success', 'Activity "'.$activity->title.'" submitted for admin review.');
    }

    public function edit(Activity $activity): View
    {
        abort_unless($activity->author_id === auth()->id(), 403);

        return view('vendor::activities.edit', $this->service->editData($activity));
    }

    public function update(UpdateActivityRequest $request, Activity $activity): RedirectResponse
    {
        abort_unless($activity->author_id === auth()->id(), 403);

        $this->service->save($request->validated(), isVendor: true, activity: $activity);

        return redirect()->route('vendor.activities.index')
            ->with('success', 'Activity "'.$activity->title.'" updated. Awaiting admin review.');
    }

    public function destroy(Activity $activity): RedirectResponse
    {
        abort_unless($activity->author_id === auth()->id(), 403);

        $title = $this->service->delete($activity);

        return back()->with('success', 'Activity "'.$title.'" deleted.');
    }
}
