<x-admin::layouts.master>
    <x-slot name="title">Vendor Requests</x-slot>

    {{-- ── Page Header ── --}}
    <div class="mb-4">
        <h1 class="fw-bold text-dark mb-1" style="font-size:1.2rem;">Vendor Requests</h1>
        <p class="text-muted small mb-0">Review and manage customer requests to become a vendor.</p>
    </div>

    {{-- ── Flash ── --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-3 mb-4" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Status Tabs --}}
    <div class="d-flex gap-2 flex-nowrap overflow-auto pb-1 mb-4" style="scrollbar-width:thin;">
        @php
            $statusTabStyles = [
                'pending' => ['bg' => '#fef3c7', 'fg' => '#92400e', 'active_bg' => '#111827', 'active_fg' => '#ffffff'],
                'approved' => ['bg' => '#e0f2fe', 'fg' => '#0369a1', 'active_bg' => '#0369a1', 'active_fg' => '#ffffff'],
                'docs_submitted' => ['bg' => '#ede9fe', 'fg' => '#5b21b6', 'active_bg' => '#5b21b6', 'active_fg' => '#ffffff'],
                'rejected' => ['bg' => '#fee2e2', 'fg' => '#991b1b', 'active_bg' => '#dc2626', 'active_fg' => '#ffffff'],
                'verified' => ['bg' => '#dcfce7', 'fg' => '#166534', 'active_bg' => '#16a34a', 'active_fg' => '#ffffff'],
            ];
        @endphp
        @foreach(['pending' => 'Pending', 'approved' => 'Approved', 'docs_submitted' => 'Docs Submitted', 'rejected' => 'Rejected', 'verified' => 'Verified'] as $key => $label)
            @php $tab = $statusTabStyles[$key]; @endphp
            <a href="{{ route('admin.vendor-requests.index', ['status' => $key]) }}"
               class="btn fw-semibold rounded-4 d-inline-flex align-items-center gap-2 flex-shrink-0 {{ $activeStatus === $key ? '' : 'border' }}"
               style="font-size:13px;padding:10px 16px;line-height:1;background:{{ $activeStatus === $key ? $tab['active_bg'] : $tab['bg'] }};color:{{ $activeStatus === $key ? $tab['active_fg'] : $tab['fg'] }};border-color:{{ $activeStatus === $key ? $tab['active_bg'] : 'rgba(15,23,42,.18)' }};">
                <span>{{ $label }}</span>
                <span class="badge rounded-pill"
                      style="min-width:24px;height:24px;display:inline-flex;align-items:center;justify-content:center;font-size:11px;background:rgba(255,255,255,.22);color:{{ $activeStatus === $key ? '#fff' : $tab['fg'] }};">
                    {{ $counts[$key] }}
                </span>
            </a>
        @endforeach
    </div>

    {{-- ── Table ── --}}
    <div class="card border rounded-4 shadow-sm" style="border-color:rgba(0,0,0,0.06)!important;">
        <div class="card-body p-0">
            @if($requests->isEmpty())
                <div class="text-center py-5 text-muted">
                    <svg width="40" height="40" fill="none" stroke="currentColor" viewBox="0 0 24 24" class="mb-3 opacity-25">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                    </svg>
                    <p class="mb-0 small">No <strong>{{ $activeStatus }}</strong> vendor requests.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" style="font-size:13px;">
                        <thead>
                            <tr class="text-uppercase text-muted border-bottom" style="font-size:10px;letter-spacing:.05em;">
                                <th class="fw-semibold ps-4 py-3">User</th>
                                <th class="fw-semibold py-3">Email</th>
                                <th class="fw-semibold py-3">Phone</th>
                                <th class="fw-semibold py-3">Requested</th>
                                <th class="fw-semibold py-3">Status</th>
                                @if(in_array($activeStatus, ['pending', 'docs_submitted', 'verified']))
                                    <th class="fw-semibold py-3 text-end pe-4">Actions</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($requests as $user)
                                <tr>
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold flex-shrink-0"
                                                 style="width:36px;height:36px;font-size:13px;background:linear-gradient(135deg,#0f1f38,#3ab5d4);">
                                                {{ strtoupper(substr($user->first_name, 0, 1)) }}{{ strtoupper(substr($user->last_name ?? '', 0, 1)) }}
                                            </div>
                                            <div>
                                                <p class="fw-semibold text-dark mb-0">{{ $user->name }}</p>
                                                @if($user->business_name)
                                                    <p class="text-muted mb-0" style="font-size:11px;">{{ $user->business_name }}</p>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-muted">{{ $user->email }}</td>
                                    <td class="text-muted">
                                        {{ $user->phone_country_code }}{{ $user->phone ?? '—' }}
                                    </td>
                                    <td class="text-muted">{{ $user->updated_at->diffForHumans() }}</td>
                                    <td>
                                        @php $status = $user->vendor_status; @endphp
                                        <span class="badge rounded-pill fw-semibold"
                                              style="font-size:11px;
                                                background:{{ match($status?->value) {
                                                    'pending'       => 'rgba(234,179,8,0.12)',
                                                    'approved'      => 'rgba(14,165,233,0.12)',
                                                    'docs_submitted'=> 'rgba(99,102,241,0.12)',
                                                    'verified'      => 'rgba(22,163,74,0.12)',
                                                    'rejected'      => 'rgba(239,68,68,0.12)',
                                                    default         => 'rgba(0,0,0,0.06)',
                                                } }};
                                                color:{{ match($status?->value) {
                                                    'pending'       => '#a16207',
                                                    'approved'      => '#0369a1',
                                                    'docs_submitted'=> '#4f46e5',
                                                    'verified'      => '#15803d',
                                                    'rejected'      => '#dc2626',
                                                    default         => '#6b7280',
                                                } }};">
                                            {{ $status?->label() ?? '—' }}
                                        </span>
                                    </td>
                                    @if($activeStatus === 'pending')
                                        <td class="text-end pe-4">
                                            <div class="d-flex justify-content-end gap-2">
                                                <form method="POST" action="{{ route('admin.vendor-requests.approve', $user) }}">
                                                    @csrf
                                                    <button type="submit"
                                                            class="btn btn-sm fw-semibold text-white"
                                                            style="background:linear-gradient(135deg,#15803d,#16a34a);border:none;border-radius:7px;font-size:11px;padding:4px 12px;"
                                                            onclick="return confirm('Approve vendor request for {{ addslashes($user->name) }}?')">
                                                        Approve
                                                    </button>
                                                </form>
                                                <form method="POST" action="{{ route('admin.vendor-requests.reject', $user) }}">
                                                    @csrf
                                                    <button type="submit"
                                                            class="btn btn-sm fw-semibold"
                                                            style="background:rgba(239,68,68,0.1);color:#dc2626;border:none;border-radius:7px;font-size:11px;padding:4px 12px;"
                                                            onclick="return confirm('Reject vendor request for {{ addslashes($user->name) }}?')">
                                                        Reject
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    @elseif($activeStatus === 'docs_submitted' || $activeStatus === 'verified')
                                        <td class="text-end pe-4">
                                            <a href="{{ route('admin.vendor-requests.documents', $user) }}"
                                               class="btn btn-sm fw-semibold"
                                               style="background:rgba(14,165,233,0.1);color:#0369a1;border:none;border-radius:7px;font-size:11px;padding:4px 12px;">
                                                Review Docs
                                            </a>
                                        </td>
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if($requests->hasPages())
                    <div class="px-4 py-3 border-top">
                        {{ $requests->appends(['status' => $activeStatus])->links() }}
                    </div>
                @endif
            @endif
        </div>
    </div>

</x-admin::layouts.master>
