@extends('layouts.master')

@section('title', 'Privacy Policy')

@section('content')
<div class="privacy-policy-page">
    <section class="privacy-hero">
        <div class="container">
            <h1 class="privacy-title">Privacy Policy</h1>
            <p class="privacy-subtitle">How Travolyo.com collects, uses, and protects your personal data in the UAE market.</p>
        </div>
    </section>

    <section class="privacy-content">
        <div class="container">
            <div class="privacy-card">
                <div class="privacy-meta">
                    <div><strong>Effective Date:</strong> 22 Nov 25</div>
                    <div><strong>Entity Responsible:</strong> LAVENDAR HOTEL MANAGEMENT LLC, DUBAI, UAE</div>
                </div>

                <div class="privacy-section">
                    <h2>1. Introduction</h2>
                    <p>Travolyo.com ("Travolyo," "we," "our," "us") values your privacy. This Privacy Policy explains how we collect, use, disclose, and protect your personal data when you use our website, admin portal and mobile apps.</p>
                    <p>We comply with the UAE Federal Decree-Law No. 45 of 2021 on Personal Data Protection (PDPL) and other applicable UAE regulations.</p>
                </div>

                <div class="privacy-section">
                    <h2>2. Information We Collect</h2>
                    <p>We may collect the following categories of personal data:</p>
                    <ul>
                        <li><strong>Identity Data:</strong> name, nationality, passport or ID details where required for bookings.</li>
                        <li><strong>Contact Data:</strong> email address, phone number, billing address.</li>
                        <li><strong>Payment Data:</strong> credit or debit card details processed securely via approved UAE payment gateways. We use Stripe, and Stripe privacy terms also apply to payment processing.</li>
                        <li><strong>Booking Data:</strong> travel preferences, accommodation details, flight information, and event information.</li>
                        <li><strong>Technical Data:</strong> IP address, browser type, device identifiers, and cookies.</li>
                        <li><strong>Location Data:</strong> location details used to improve search results and relevance.</li>
                    </ul>
                </div>

                <div class="privacy-section">
                    <h2>3. How We Use Your Information</h2>
                    <p>We use your personal data to:</p>
                    <ul>
                        <li>Process bookings and payments.</li>
                        <li>Provide customer support.</li>
                        <li>Send booking confirmations and updates.</li>
                        <li>Improve our website and mobile apps.</li>
                        <li>Comply with UAE legal and regulatory requirements.</li>
                        <li>Prevent fraud and support cybersecurity.</li>
                    </ul>
                </div>

                <div class="privacy-section">
                    <h2>4. Sharing of Information</h2>
                    <p>We may share your data with:</p>
                    <ul>
                        <li>Travel suppliers such as hotels, airlines, tour operators, and event managers to fulfill bookings.</li>
                        <li>Payment processors authorized by the UAE Central Bank, including Stripe.</li>
                        <li>Government authorities when legally required, such as immigration or tourism regulators.</li>
                        <li>Service providers such as IT, support, and marketing partners under confidentiality obligations.</li>
                    </ul>
                    <p>We do not sell your personal data.</p>
                </div>

                <div class="privacy-section">
                    <h2>5. International Transfers</h2>
                    <p>Some travel suppliers may be located outside the UAE. In such cases, we ensure appropriate safeguards are in place in line with the UAE PDPL.</p>
                </div>

                <div class="privacy-section">
                    <h2>6. Data Retention</h2>
                    <p>We retain personal data only as long as necessary to fulfill bookings, comply with legal obligations, and resolve disputes.</p>
                    <p>Payment data is stored in compliance with PCI DSS standards and UAE Central Bank regulations.</p>
                </div>

                <div class="privacy-section">
                    <h2>7. Your Rights</h2>
                    <p>Under UAE law, you have the right to:</p>
                    <ul>
                        <li>Access your personal data.</li>
                        <li>Request correction or deletion.</li>
                        <li>Withdraw consent for processing.</li>
                        <li>Object to certain uses of your data.</li>
                    </ul>
                    <p>Requests can be made via <a href="mailto:privacy@travolyo.com">privacy@travolyo.com</a>.</p>
                </div>

                <div class="privacy-section">
                    <h2>8. Cookies & Tracking</h2>
                    <p>Travolyo uses cookies and similar technologies to:</p>
                    <ul>
                        <li>Enhance user experience.</li>
                        <li>Analyze website traffic.</li>
                        <li>Provide personalized offers.</li>
                    </ul>
                    <p>You may disable cookies in your browser, but some features may not function properly.</p>
                </div>

                <div class="privacy-section">
                    <h2>9. Security</h2>
                    <p>We implement technical and organizational measures to protect your data, including:</p>
                    <ul>
                        <li>Encryption of payment transactions.</li>
                        <li>Secure servers hosted in the UAE or approved jurisdictions.</li>
                        <li>Regular audits and compliance checks.</li>
                    </ul>
                </div>

                <div class="privacy-section">
                    <h2>10. Children's Privacy</h2>
                    <p>Travolyo does not knowingly collect personal data from children under 18 without parental consent. Parents and guardians must supervise bookings for minors.</p>
                </div>

                <div class="privacy-section">
                    <h2>11. Changes to This Policy</h2>
                    <p>We may update this Privacy Policy from time to time. Updates will be posted on Travolyo.com with a revised effective date.</p>
                </div>
            </div>
        </div>
    </section>
</div>

<style>
    .privacy-policy-page {
        font-family: "Inter", sans-serif;
        color: #1d2025;
    }

    .privacy-hero {
        background: linear-gradient(180deg, rgba(5, 168, 199, 0.22) 0%, #ffffff 55%, rgba(241, 245, 249, 0.12) 100%);
        padding: 100px 0 80px;
        text-align: center;
    }

    .privacy-title {
        margin-bottom: 20px;
        color: #1d2025;
        font-weight: 800;
        font-size: 48px;
        line-height: 1.05;
    }

    .privacy-subtitle {
        font-size: 18px;
        color: #65758b;
        max-width: 860px;
        margin: 0 auto;
        line-height: 1.7;
    }

    .privacy-policy-page section {
        padding: 70px 0;
    }

    .privacy-content {
        background: #ffffff;
    }

    .privacy-card {
        border: 1px solid #e6edf5;
        border-radius: 16px;
        padding: 36px 40px;
        background: #f9fbfe;
        box-shadow: 0 2px 10px rgba(16, 24, 40, 0.06);
    }

    .privacy-meta {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 16px 24px;
        margin-bottom: 24px;
        font-size: 15px;
        color: #4b5563;
    }

    .privacy-section {
        margin-bottom: 28px;
    }

    .privacy-section:last-child {
        margin-bottom: 0;
    }

    .privacy-section h2 {
        font-size: 20px;
        font-weight: 700;
        margin-bottom: 10px;
        color: #1d2025;
    }

    .privacy-section p,
    .privacy-section li {
        margin-bottom: 10px;
        font-size: 15px;
        line-height: 26px;
        color: #5b667a;
    }

    .privacy-section ul {
        margin: 0 0 10px 0;
        padding-left: 18px;
        color: #5b667a;
    }

    .privacy-section strong {
        color: #1d2025;
    }

    .privacy-section a {
        color: #05a8c7;
        font-weight: 700;
        text-decoration: none;
    }

    .privacy-section a:hover {
        text-decoration: underline;
    }

    @media (max-width: 768px) {
        .privacy-hero {
            padding: 70px 0 60px;
        }

        .privacy-title {
            font-size: 36px;
            line-height: 40px;
        }

        .privacy-subtitle {
            font-size: 16px;
        }

        .privacy-card {
            padding: 24px;
        }
    }
</style>
@endsection
