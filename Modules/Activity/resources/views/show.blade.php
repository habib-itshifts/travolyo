@extends('layouts.master')

@section('title', $activity->title . ' - Travolyo')

@push('styles')
<style>
    .activity-detail-page {
        background: #f7fafc;
        padding: 56px 0 80px;
    }
    .activity-detail-shell,
    .activity-side-card,
    .activity-gallery-card {
        background: #fff;
        border-radius: 28px;
        box-shadow: 0 18px 45px rgba(15, 23, 42, 0.08);
    }
    .activity-detail-shell {
        overflow: hidden;
    }
    .activity-detail-hero {
        position: relative;
        min-height: 380px;
        background: linear-gradient(135deg, #cbd5e1 0%, #e2e8f0 100%);
    }
    .activity-detail-hero img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }
    .activity-detail-body {
        padding: 34px;
    }
    .activity-gallery-card,
    .activity-side-card {
        padding: 24px;
    }
    .activity-gallery-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 14px;
    }
    .activity-gallery-grid img {
        width: 100%;
        height: 155px;
        object-fit: cover;
        border-radius: 16px;
        border: 1px solid #e2e8f0;
    }
    .activity-related-link {
        display: flex;
        gap: 14px;
        text-decoration: none;
        color: inherit;
    }
    .activity-related-link + .activity-related-link {
        margin-top: 16px;
        padding-top: 16px;
        border-top: 1px solid #e2e8f0;
    }
    .activity-related-thumb {
        width: 90px;
        height: 76px;
        border-radius: 16px;
        overflow: hidden;
        background: linear-gradient(135deg, #cbd5e1 0%, #e2e8f0 100%);
        flex-shrink: 0;
    }
    .activity-related-thumb img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .activity-highlight-list {
        display: grid;
        gap: 12px;
    }
    .activity-highlight-item {
        padding: 14px 16px;
        border-radius: 16px;
        background: #f8fafc;
        color: #334155;
    }
</style>
@endpush

@section('content')
<section class="activity-detail-page">
    <div class="container">
        <div class="row g-4">
            <div class="col-xl-8">
                <article class="activity-detail-shell">
                    <div class="activity-detail-hero">
                        @if ($activity->image_url)
                            <img src="{{ $activity->image_url }}" alt="{{ $activity->title }}">
                        @endif
                    </div>
                    <div class="activity-detail-body">
                        <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-3">
                            <div>
                                <div class="text-uppercase fw-semibold text-muted mb-2" style="font-size:.78rem;letter-spacing:.16em;">
                                    {{ $activity->category ?: 'Activity' }}
                                </div>
                                <h1 class="fw-bold text-dark mb-2">{{ $activity->title }}</h1>
                                <p class="text-muted mb-0">
                                    @if ($activity->city)
                                        {{ $activity->city }}
                                    @endif
                                    @if ($activity->country)
                                        {{ $activity->city ? ', ' : '' }}{{ $activity->country }}
                                    @endif
                                    @if ($activity->address)
                                        <span class="mx-1">|</span>{{ $activity->address }}
                                    @endif
                                </p>
                            </div>
                            <div class="text-xl-end">
                                <div class="h4 fw-bold text-dark mb-1">{{ $activity->convertedCurrency ?: $activity->baseCurrency ?: 'AED' }} {{ number_format((float) ($activity->price_per_person ?: 0), 2) }}</div>
                                <div class="text-muted">per person</div>
                            </div>
                        </div>

                        <div class="d-flex flex-wrap gap-2 mb-4">
                            @if ($activity->duration)
                                <span class="badge rounded-pill bg-light text-dark border px-3 py-2">{{ $activity->duration }}</span>
                            @endif
                            @if ($activity->max_participants)
                                <span class="badge rounded-pill bg-light text-dark border px-3 py-2">Up to {{ $activity->max_participants }} participants</span>
                            @endif
                            <span class="badge rounded-pill {{ $activity->instant_confirmation ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary' }} px-3 py-2">
                                {{ $activity->instant_confirmation ? 'Instant confirmation' : 'Manual confirmation' }}
                            </span>
                        </div>

                        <div class="mb-4">
                            <h2 class="h5 fw-bold text-dark mb-3">Description</h2>
                            <div class="text-muted" style="line-height:1.85;">
                                {!! nl2br(e($activity->description)) !!}
                            </div>
                        </div>

                        @if (!empty($activity->extra_information))
                            <div>
                                <h2 class="h5 fw-bold text-dark mb-3">Extra Information</h2>
                                <div class="activity-highlight-list">
                                    @foreach ($activity->extra_information as $item)
                                        <div class="activity-highlight-item">{{ $item }}</div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </article>

                @if (!empty($activity->gallery_urls))
                    <div class="activity-gallery-card mt-4">
                        <h2 class="h5 fw-bold text-dark mb-3">Gallery</h2>
                        <div class="activity-gallery-grid">
                            @foreach ($activity->gallery_urls as $image)
                                <img src="{{ $image }}" alt="{{ $activity->title }}">
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <div class="col-xl-4">
                <div class="activity-side-card mb-4">
                    <h3 class="h5 fw-bold mb-3">Trip Info</h3>
                    <div class="d-grid gap-2 text-muted">
                        <div><strong class="text-dark">Selected Date:</strong> {{ \Carbon\Carbon::parse($selectedDate)->format('d M Y') }}</div>
                        <div><strong class="text-dark">Author:</strong> {{ $activity->author?->name ?? 'Travolyo Team' }}</div>
                        <div><strong class="text-dark">Status:</strong> {{ $activity->is_active ? 'Active' : 'Inactive' }}</div>
                    </div>
                    <a href="{{ route('activities.checkout', ['activity' => $activity, 'city' => $activity->city, 'date' => $selectedDate, 'participants' => $selectedParticipants ?? 1]) }}"
                       class="btn btn-dark w-100 rounded-pill mt-4">Book This Activity</a>
                </div>

                <div class="activity-side-card">
                    <h3 class="h5 fw-bold mb-3">Related Activities</h3>
                    @forelse ($relatedActivities as $relatedActivity)
                        <a href="{{ route('activities.show', ['activity' => $relatedActivity, 'activity_date' => $selectedDate, 'participants' => $selectedParticipants ?? 1]) }}" class="activity-related-link">
                            <div class="activity-related-thumb">
                                @if ($relatedActivity->image_url)
                                    <img src="{{ $relatedActivity->image_url }}" alt="{{ $relatedActivity->title }}">
                                @endif
                            </div>
                            <div>
                                <div class="fw-semibold text-dark mb-1">{{ $relatedActivity->title }}</div>
                                <div class="small text-muted">
                                    {{ $relatedActivity->city ?: 'Activity' }}
                                    @if ($relatedActivity->price_per_person)
                                        <span class="mx-1">|</span>{{ $relatedActivity->currency ?: 'AED' }} {{ number_format((float) $relatedActivity->price_per_person, 2) }}
                                    @endif
                                </div>
                            </div>
                        </a>
                    @empty
                        <p class="text-muted mb-0">No related activities available yet.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
