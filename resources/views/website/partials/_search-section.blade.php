@php
    $activeTab = $activeTab
        ?? (request()->routeIs('flights.*')
            ? 'flights'
            : (request()->routeIs('activities.*')
                ? 'activities'
                : 'hotels'));
@endphp

@push('styles')
<style>
.shared-search-hero {
    background: #f7f8fb;
    overflow: visible;
    padding: 18px 0 150px;
    position: relative;
    z-index: 12;
}
.shared-search-hero__panel {
    background: url('{{ asset('assets/images/website/background-image.png') }}') center/cover no-repeat;
    border-radius: 28px;
    box-shadow:
        inset 0 0 0 1px rgba(255, 255, 255, 0.24),
        0 22px 44px rgba(18, 38, 63, 0.08);
    min-height: 540px;
    overflow: visible;
    padding: 26px 0 150px;
    position: relative;
    z-index: 12;
}
.shared-search-hero__panel::before {
    background: linear-gradient(180deg, rgba(255, 255, 255, 0.06), rgba(255, 255, 255, 0.02) 38%, rgba(255, 255, 255, 0.10) 100%);
    border: 1px solid rgba(255, 255, 255, 0.42);
    border-radius: inherit;
    box-shadow:
        inset 0 0 0 1px rgba(255, 255, 255, 0.10),
        inset 0 -12px 26px rgba(255, 255, 255, 0.08);
    content: "";
    inset: 0;
    pointer-events: none;
    position: absolute;
}
.shared-search-hero__panel::after {
    background: none;
    content: "";
    inset: 0;
    pointer-events: none;
    position: absolute;
}
.shared-search-hero__panel .shared-search-hero__mountain {
    display: none;
}
.shared-search-hero__panel .shared-search-hero__mountain--left {
    background: linear-gradient(180deg, rgba(255,255,255,0.14), rgba(255,255,255,0.02));
    clip-path: polygon(0 100%, 14% 76%, 28% 66%, 42% 48%, 56% 57%, 72% 36%, 100% 100%);
    height: 228px;
    left: 0;
    width: 32%;
}
.shared-search-hero__panel .shared-search-hero__mountain--right {
    background: linear-gradient(180deg, rgba(255,255,255,0.14), rgba(255,255,255,0.02));
    clip-path: polygon(0 100%, 22% 58%, 40% 40%, 56% 54%, 76% 30%, 100% 100%);
    height: 214px;
    right: 0;
    width: 34%;
}
.shared-search-hero__shell {
    position: relative;
    z-index: 1;
}
.shared-search-hero__copy {
    margin: 0 auto 10px;
    max-width: 620px;
    text-align: center;
}
.shared-search-hero__title {
    color: #ffffff;
    font-size: clamp(2rem, 3.1vw, 2.6rem);
    font-weight: 800;
    letter-spacing: -0.04em;
    margin-bottom: 10px;
    text-shadow: 0 10px 28px rgba(3, 45, 72, 0.18);
}
.shared-search-hero__trustbar {
    align-items: center;
    display: flex;
    flex-wrap: wrap;
    gap: 12px 24px;
    justify-content: center;
    margin-bottom: 16px;
}
.shared-search-hero__trustbar-item {
    align-items: center;
    color: rgba(255, 255, 255, 0.92);
    display: inline-flex;
    font-size: 0.83rem;
    font-weight: 600;
    gap: 8px;
}
.shared-search-hero__trustbar-item i {
    align-items: center;
    background: rgba(255, 255, 255, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.18);
    border-radius: 999px;
    display: inline-flex;
    font-size: .72rem;
    height: 20px;
    justify-content: center;
    width: 20px;
}
@media (max-width: 991.98px) {
    .shared-search-hero__panel {
        min-height: 500px;
        padding-bottom: 110px;
    }
    .shared-search-hero__panel .shared-search-hero__mountain { bottom: 64px; }
}
@media (max-width: 767.98px) {
    .shared-search-hero {
        padding: 14px 0 28px;
    }
    .shared-search-hero__panel {
        border-radius: 24px;
        min-height: auto;
        overflow: hidden;
        padding: 24px 0 24px;
    }
    .shared-search-hero__panel .shared-search-hero__mountain { display: none; }
    .shared-search-hero__copy {
        margin-bottom: 8px;
        max-width: 100%;
    }
    .shared-search-hero__trustbar {
        gap: 10px 14px;
        margin-bottom: 14px;
    }
    .shared-search-hero__trustbar-item {
        font-size: 0.78rem;
        gap: 6px;
    }
    .shared-search-hero__trustbar-item i {
        height: 20px;
        width: 20px;
    }
}
</style>
@endpush

<section class="shared-search-hero">
    <div class="container">
        <div class="shared-search-hero__panel">
            <span class="shared-search-hero__mountain shared-search-hero__mountain--left"></span>
            <span class="shared-search-hero__mountain shared-search-hero__mountain--right"></span>
            <div class="container shared-search-hero__shell">
                <div class="shared-search-hero__copy">
                    <h1 class="shared-search-hero__title">Your Trip Starts Here</h1>
                    <div class="shared-search-hero__trustbar">
                        <span class="shared-search-hero__trustbar-item"><i class="bi bi-check2"></i>Secure payment</span>
                        <span class="shared-search-hero__trustbar-item"><i class="bi bi-headset"></i>Support in approx. 30s</span>
                    </div>
                </div>

                @include('website.partials._search-widget', [
                    'activeTab' => $activeTab,
                    'widgetVariant' => 'hero',
                ])
            </div>
        </div>
    </div>
</section>
