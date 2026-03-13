@extends('layouts.master')

@section('title', 'Help Center')

@section('content')
<div class="help-center-page">
    <section class="help-hero">
        <div class="container">
            <h1 class="help-title">Help Center</h1>
            <p class="help-subtitle">Support and guidance to help you explore Travolyo with confidence.</p>
        </div>
    </section>

    <section class="help-content">
        <div class="container">
            <div class="help-card">
                <div class="help-section">
                    <p>We know travel planning can raise questions. Our Help Center is designed to provide clear answers and reliable support whenever you need it.</p>
                </div>

                <div class="help-section">
                    <h2>Getting Started</h2>
                    <p>Explore destinations and discover stays through a visually guided, intuitive experience. Travolyo is designed to help you browse with ease and make informed travel decisions.</p>
                </div>

                <div class="help-section">
                    <h2>Destinations &amp; Stays</h2>
                    <p>Browse destinations, view hotel details, and explore amenities through a clear and well-structured experience designed to support confident choices.</p>
                </div>

                <div class="help-section">
                    <h2>Account &amp; Preferences</h2>
                    <p>Update your details, review saved preferences, and personalize your experience so the destinations and stays you see align with your interests.</p>
                </div>

                <div class="help-section">
                    <h2>Payments &amp; Data Safety</h2>
                    <p>Travolyo follows industry-standard practices to protect user data and maintain a secure digital environment. Your privacy and safety are central to how we operate.</p>
                </div>

                <div class="help-section">
                    <h2>Contact Support</h2>
                    <p><strong>Need More Help?</strong></p>
                    <p>Our support team is here to assist you whenever you need further guidance.</p>
                    <p><strong>Support:</strong> <a href="mailto:support@travolyo.com">support@travolyo.com</a></p>
                </div>
            </div>
        </div>
    </section>
</div>

<style>
    .help-center-page {
        font-family: "Inter", sans-serif;
        color: #1d2025;
    }

    .help-hero {
        background: linear-gradient(180deg, rgba(5, 168, 199, 0.22) 0%, #ffffff 55%, rgba(241, 245, 249, 0.12) 100%);
        padding: 100px 0 80px;
        text-align: center;
    }

    .help-title {
        margin-bottom: 20px;
        color: #1d2025;
        font-weight: 800;
        font-size: 48px;
        line-height: 1.05;
    }

    .help-subtitle {
        font-size: 18px;
        color: #65758b;
        max-width: 860px;
        margin: 0 auto;
        line-height: 1.7;
    }

    .help-center-page section {
        padding: 70px 0;
    }

    .help-content {
        background: #ffffff;
    }

    .help-card {
        border: 1px solid #e6edf5;
        border-radius: 16px;
        padding: 36px 40px;
        background: #f9fbfe;
        box-shadow: 0 2px 10px rgba(16, 24, 40, 0.06);
    }

    .help-section {
        margin-bottom: 28px;
    }

    .help-section:last-child {
        margin-bottom: 0;
    }

    .help-section h2 {
        font-size: 20px;
        font-weight: 700;
        margin-bottom: 10px;
        color: #1d2025;
    }

    .help-section p,
    .help-section li {
        margin-bottom: 10px;
        font-size: 15px;
        line-height: 26px;
        color: #5b667a;
    }

    .help-section a {
        color: #05a8c7;
        font-weight: 700;
        text-decoration: none;
    }

    .help-section a:hover {
        text-decoration: underline;
    }

    @media (max-width: 768px) {
        .help-hero {
            padding: 70px 0 60px;
        }

        .help-title {
            font-size: 36px;
            line-height: 40px;
        }

        .help-subtitle {
            font-size: 16px;
        }

        .help-card {
            padding: 24px;
        }
    }
</style>
@endsection
