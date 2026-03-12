@if(!empty($passengers) && count($passengers) > 0)
<div class="detail-card">
    <div class="detail-card__header">
        <div class="detail-card__icon"><i class="bi bi-people"></i></div>
        <div>
            <p class="detail-card__title">Passengers</p>
            <p class="detail-card__subtitle">{{ count($passengers) }} passenger{{ count($passengers) > 1 ? 's' : '' }}</p>
        </div>
    </div>
    <div class="detail-card__body p-0">
        <div class="table-responsive">
            <table class="pax-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>DOB</th>
                        <th>Nationality</th>
                        <th>Passport</th>
                        <th>Expiry</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($passengers as $i => $pax)
                    <tr>
                        <td class="text-muted">{{ $i + 1 }}</td>
                        <td>
                            <strong>{{ $pax['title'] ?? '' }} {{ $pax['first_name'] ?? '' }} {{ $pax['last_name'] ?? '' }}</strong>
                            <div style="font-size:.75rem; color:var(--text-muted);">
                                {{ $pax['gender'] === 'M' ? 'Male' : 'Female' }}
                            </div>
                        </td>
                        <td>{{ $pax['dob'] ?? '—' }}</td>
                        <td>{{ $pax['nationality'] ?? '—' }}</td>
                        <td>{{ $pax['passport'] ?? '—' }}</td>
                        <td>{{ $pax['passport_expiry'] ?? '—' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif
