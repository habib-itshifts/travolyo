@extends('layouts.master')

@section('title', 'Cookies Policy')

@section('canonical', 'https://www.travolyo.com/cookies-policy')

@section('content')
<div class="cookies-policy-page">
    <section class="cookies-hero">
        <div class="container">
            <h1 class="cookies-title">Cookies Policy</h1>
            <p class="cookies-subtitle">How Travolyo uses cookies and similar technologies to support website functionality and improve your experience.</p>
        </div>
    </section>

    <section class="cookies-content">
        <div class="container">
            <div class="cookies-card">
                <div class="cookies-section">
                    <p>This policy explains how Travolyo uses cookies and similar technologies to support website functionality, analyze performance, and improve user experience.</p>
                </div>

                <div class="cookies-section">
                    <h2>Understanding Cookies</h2>
                    <p>Cookies are small text files stored on your device when you visit a website. They help websites function efficiently, remember preferences, and provide insights into usage patterns.</p>
                </div>

                <div class="cookies-section">
                    <h2>Cookies Used on Travolyo</h2>
                    <p>Travolyo uses the following types of cookies:</p>
                    <ul>
                        <li><strong>Essential Cookies:</strong> Required for basic website functionality and security.</li>
                        <li><strong>Functional Cookies:</strong> Remember user preferences and settings.</li>
                        <li><strong>Analytics Cookies:</strong> Help us understand how users interact with the platform so we can improve performance.</li>
                    </ul>
                </div>

                <div class="cookies-section">
                    <h2>Why We Use Cookies</h2>
                    <p>Cookies help Travolyo:</p>
                    <ul>
                        <li>Maintain stable website performance.</li>
                        <li>Improve usability.</li>
                        <li>Enhance the overall experience for visitors.</li>
                    </ul>
                </div>

                <div class="cookies-section">
                    <h2>Managing Cookies</h2>
                    <p>You can manage or disable cookies through your browser settings. Please note that limiting cookies may affect certain site features.</p>
                </div>

                <div class="cookies-section">
                    <h2>Policy Updates</h2>
                    <p>This Cookies Policy may be updated periodically to reflect legal, technical, or operational changes. Any updates will be published on this page.</p>
                </div>

                <div class="cookies-section">
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
    .cookies-policy-page {
        font-family: "Inter", sans-serif;
        color: #1d2025;
    }

    .cookies-hero {
        background: linear-gradient(180deg, rgba(5, 168, 199, 0.22) 0%, #ffffff 55%, rgba(241, 245, 249, 0.12) 100%);
        padding: 100px 0 80px;
        text-align: center;
    }

    .cookies-title {
        margin-bottom: 20px;
        color: #1d2025;
        font-weight: 800;
        font-size: 48px;
        line-height: 1.05;
    }

    .cookies-subtitle {
        font-size: 18px;
        color: #65758b;
        max-width: 860px;
        margin: 0 auto;
        line-height: 1.7;
    }

    .cookies-policy-page section {
        padding: 70px 0;
    }

    .cookies-content {
        background: #ffffff;
    }

    .cookies-card {
        border: 1px solid #e6edf5;
        border-radius: 16px;
        padding: 36px 40px;
        background: #f9fbfe;
        box-shadow: 0 2px 10px rgba(16, 24, 40, 0.06);
    }

    .cookies-section {
        margin-bottom: 28px;
    }

    .cookies-section:last-child {
        margin-bottom: 0;
    }

    .cookies-section h2 {
        font-size: 20px;
        font-weight: 700;
        margin-bottom: 10px;
        color: #1d2025;
    }

    .cookies-section p,
    .cookies-section li {
        margin-bottom: 10px;
        font-size: 15px;
        line-height: 26px;
        color: #5b667a;
    }

    .cookies-section ul {
        margin: 0 0 10px 0;
        padding-left: 18px;
        color: #5b667a;
    }

    .cookies-section a {
        color: #05a8c7;
        font-weight: 700;
        text-decoration: none;
    }

    .cookies-section a:hover {
        text-decoration: underline;
    }

    @media (max-width: 768px) {
        .cookies-hero {
            padding: 70px 0 60px;
        }

        .cookies-title {
            font-size: 36px;
            line-height: 40px;
        }

        .cookies-subtitle {
            font-size: 16px;
        }

        .cookies-card {
            padding: 24px;
        }
    }
</style>
@endsection
