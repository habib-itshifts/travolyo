@extends('layouts.master')

@section('title', 'Terms of Services')

@section('content')
<div class="terms-conditions-page">
    <section class="terms-hero">
        <div class="container">
            <h1 class="terms-title">Terms of Services</h1>
            <p class="terms-subtitle">Please read these terms carefully before using Travolyo.com.</p>
        </div>
    </section>

    <section class="terms-content">
        <div class="container">
            <div class="terms-card">
                <div class="terms-meta">
                    <div><strong>Effective Date:</strong> 22 Nov 25</div>
                    <div><strong>Entity Responsible:</strong> LAVENDAR HOTEL MANAGEMENT LLC, DUBAI, UAE</div>
                </div>

                <div class="terms-section">
                    <h2>1. Acceptance of Terms</h2>
                    <p>By accessing Travolyo.com and Travolyo mobile apps, you agree to these terms and conditions, our Privacy Policy, and any policies referenced herein. If you do not agree, you must discontinue use immediately.</p>
                    <p>These terms comply with UAE Federal Law No. 24 of 2006 on Consumer Protection and relevant Dubai Department of Economy and Tourism regulations.</p>
                </div>

                <div class="terms-section">
                    <h2>2. Eligibility</h2>
                    <p>Users must be 18 years or older and legally capable of entering contracts under UAE law. Minors may use the platform only under the supervision of a parent or guardian.</p>
                </div>

                <div class="terms-section">
                    <h2>3. Services</h2>
                    <p>Travolyo facilitates bookings for:</p>
                    <ul>
                        <li>Hotels, serviced apartments, and other accommodations.</li>
                        <li>Flights and events.</li>
                        <li>Tours, activities, and experiences.</li>
                    </ul>
                    <p>Travolyo acts as an intermediary. The actual travel products are provided by third-party suppliers. Contracts for services are directly between you and the travel supplier or hotel management.</p>
                </div>

                <div class="terms-section">
                    <h2>4. Booking & Payment</h2>
                    <p>Bookings are confirmed once payment is processed and a confirmation email or SMS is issued.</p>
                    <p>Accepted payment methods may include credit cards, debit cards, UAE-approved digital wallets, and any other methods displayed at checkout.</p>
                    <p>Prices are shown in AED or USD unless otherwise stated. Currency conversions may apply.</p>
                    <p>Displayed prices may include VAT and any applicable UAE tourism fees where relevant.</p>
                </div>

                <div class="terms-section">
                    <h2>5. Cancellations & Refunds</h2>
                    <p>Cancellation and refund policies vary by supplier and will be displayed during the booking process.</p>
                    <p>Some bookings may be non-refundable.</p>
                    <p>Refunds, where applicable, will be processed to the original payment method.</p>
                    <p>Travolyo follows applicable UAE payment and reversal guidelines.</p>
                </div>

                <div class="terms-section">
                    <h2>6. User Obligations</h2>
                    <p>You agree not to:</p>
                    <ul>
                        <li>Make fraudulent, speculative, or fake bookings.</li>
                        <li>Resell Travolyo services without authorization.</li>
                        <li>Post unlawful, defamatory, or offensive content.</li>
                        <li>Violate UAE laws, including applicable cybercrime laws.</li>
                    </ul>
                </div>

                <div class="terms-section">
                    <h2>7. Liability Disclaimer</h2>
                    <p>Travolyo does not own or operate most travel products listed on the platform. Responsibility for service delivery lies with the relevant supplier.</p>
                    <p>Travolyo is not liable for delays, cancellations, or supplier service failures beyond its control.</p>
                    <p>To the maximum extent permitted by law, Travolyo liability is limited to the amount paid for the relevant booking.</p>
                </div>

                <div class="terms-section">
                    <h2>8. Data Protection</h2>
                    <p>Travolyo complies with the UAE Personal Data Protection Law and processes personal data in line with its Privacy Policy.</p>
                </div>

                <div class="terms-section">
                    <h2>9. Governing Law & Dispute Resolution</h2>
                    <p>These terms are governed by the laws of the United Arab Emirates.</p>
                    <p>Any disputes shall be resolved under the jurisdiction of the Dubai Courts.</p>
                    <p>Consumers may also raise complaints with the UAE Ministry of Economy Consumer Protection Department where applicable.</p>
                </div>

                <div class="terms-section">
                    <h2>10. Amendments</h2>
                    <p>Travolyo may update these terms at any time. Updates will be posted on the platform and become effective immediately.</p>
                    <p>Continued use of the platform constitutes acceptance of the revised terms.</p>
                </div>
            </div>
        </div>
    </section>
</div>

<style>
    .terms-conditions-page {
        font-family: "Inter", sans-serif;
        color: #1d2025;
    }

    .terms-hero {
        background: linear-gradient(180deg, rgba(5, 168, 199, 0.22) 0%, #ffffff 55%, rgba(241, 245, 249, 0.12) 100%);
        padding: 100px 0 80px;
        text-align: center;
    }

    .terms-title {
        margin-bottom: 20px;
        color: #1d2025;
        font-weight: 800;
        font-size: 48px;
        line-height: 1.05;
    }

    .terms-subtitle {
        font-size: 18px;
        color: #65758b;
        max-width: 860px;
        margin: 0 auto;
        line-height: 1.7;
    }

    .terms-conditions-page section {
        padding: 70px 0;
    }

    .terms-content {
        background: #ffffff;
    }

    .terms-card {
        border: 1px solid #e6edf5;
        border-radius: 16px;
        padding: 36px 40px;
        background: #f9fbfe;
        box-shadow: 0 2px 10px rgba(16, 24, 40, 0.06);
    }

    .terms-meta {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 16px 24px;
        margin-bottom: 24px;
        font-size: 15px;
        color: #4b5563;
    }

    .terms-section {
        margin-bottom: 28px;
    }

    .terms-section:last-child {
        margin-bottom: 0;
    }

    .terms-section h2 {
        font-size: 20px;
        font-weight: 700;
        margin-bottom: 10px;
        color: #1d2025;
    }

    .terms-section p,
    .terms-section li {
        margin-bottom: 10px;
        font-size: 15px;
        line-height: 26px;
        color: #5b667a;
    }

    .terms-section ul {
        margin: 0 0 10px 0;
        padding-left: 18px;
        color: #5b667a;
    }

    .terms-section strong {
        color: #1d2025;
    }

    @media (max-width: 768px) {
        .terms-hero {
            padding: 70px 0 60px;
        }

        .terms-title {
            font-size: 36px;
            line-height: 40px;
        }

        .terms-subtitle {
            font-size: 16px;
        }

        .terms-card {
            padding: 24px;
        }
    }
</style>
@endsection
