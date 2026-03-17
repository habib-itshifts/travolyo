<?php

namespace Modules\Activity\Http\Controllers\Admin;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\View\View;
use Modules\Activity\Http\Requests\Admin\StoreActivityRequest;
use Modules\Activity\Http\Requests\Admin\UpdateActivityRequest;
use Modules\Activity\Models\Activity;
use Modules\Activity\Services\ActivityService;

class ActivityController extends Controller
{
    public function __construct(private readonly ActivityService $service) {}

    public function index(Request $request): View
    {
        return view('admin::activities.index', $this->service->adminIndex($request));
    }

    public function create(): View
    {
        return view('admin::activities.create', $this->service->createData(withUsers: true));
    }

    public function store(StoreActivityRequest $request): RedirectResponse
    {
        $activity = $this->service->save($request->validated(), isVendor: false);

        return redirect()->route('admin.activities.index')
            ->with('success', 'Activity "'.$activity->title.'" created successfully.');
    }

    public function edit(Activity $activity): View
    {
        return view('admin::activities.edit', $this->service->editData($activity, withUsers: true));
    }

    public function update(UpdateActivityRequest $request, Activity $activity): RedirectResponse
    {
        $this->service->save($request->validated(), isVendor: false, activity: $activity);

        return redirect()->route('admin.activities.index')
            ->with('success', 'Activity "'.$activity->title.'" updated successfully.');
    }

    public function destroy(Activity $activity): RedirectResponse
    {
        $title = $this->service->delete($activity);

        return back()->with('success', 'Activity "'.$title.'" deleted.');
    }
}
