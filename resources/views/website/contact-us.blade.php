@extends('layouts.master')

@section('title', 'Contact Us')

@section('canonical', 'https://www.travolyo.com/contact-us')

@section('content')
<div class="contact-us-page">
    <section class="contact-hero">
        <div class="container">
            <h1 class="contact-hero-title">Contact Us</h1>
            <p class="contact-hero-subtitle">Have questions? We're here to help. Reach out to our team and we'll respond as soon as possible.</p>
        </div>
    </section>

    <section class="contact-main-section">
        <div class="container">
            <div class="contact-content-wrapper">
                <div class="contact-form-container">
                    <h2 class="form-title">Send us a message</h2>

                    <form class="contact-form" id="contactForm">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="name">Name <span class="required">*</span></label>
                                <input type="text" id="name" name="name" placeholder="Name" required>
                            </div>
                            <div class="form-group">
                                <label for="email">Email <span class="required">*</span></label>
                                <input type="email" id="email" name="email" placeholder="Email" required autocomplete="email" inputmode="email" maxlength="255">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="subject">Subject <span class="required">*</span></label>
                            <input type="text" id="subject" name="subject" placeholder="How can we help?" required>
                        </div>

                        <div class="form-group">
                            <label for="message">Message <span class="required">*</span></label>
                            <textarea id="message" name="message" rows="5" placeholder="Message" required></textarea>
                        </div>

                        <div class="form-submit-wrapper">
                            <button type="submit" class="btn-submit">SEND MESSAGE</button>
                        </div>
                        <div class="form-mess" id="formMessage"></div>
                    </form>
                </div>

                <div class="contact-info-container">
                    <div class="contact-info-item">
                        <div class="info-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="2" y="4" width="20" height="16" rx="2" />
                                <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7" />
                            </svg>
                        </div>
                        <div class="info-content">
                            <h3>Email</h3>
                            <p>support@travolyo.com</p>
                        </div>
                    </div>

                    <div class="contact-info-item">
                        <div class="info-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z" />
                            </svg>
                        </div>
                        <div class="info-content">
                            <h3>Phone</h3>
                            <p>+97142296659</p>
                        </div>
                    </div>

                    <div class="contact-info-item">
                        <div class="info-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z" />
                                <circle cx="12" cy="10" r="3" />
                            </svg>
                        </div>
                        <div class="info-content">
                            <h3>Address</h3>
                            <p>Glass Building - Office 201</p>
                            <p>Al Maktoum Rd - next to Khalidiya Palace Hotel</p>
                            <p>Al Muraqqabat - Deira - Dubai - United Arab Emirates</p>
                            <p>
                                <a class="address-map-link" href="https://maps.app.goo.gl/y8fQHo4smNJrA31c7?g_st=iw" target="_blank" rel="noopener">
                                    <span class="address-map-text">Get Location</span>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M18 13v6a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6" />
                                        <polyline points="15 3 21 3 21 9" />
                                        <line x1="10" y1="14" x2="21" y2="3" />
                                    </svg>
                                </a>
                            </p>
                        </div>
                    </div>

                    <div class="contact-info-item">
                        <div class="info-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10" />
                                <polyline points="12 6 12 12 16 14" />
                            </svg>
                        </div>
                        <div class="info-content">
                            <h3>Business Hours</h3>
                            <p>Monday - Friday: 9:00 AM - 6:00 PM</p>
                            <p>Saturday: 10:00 AM - 4:00 PM</p>
                            <p>Sunday: Closed</p>
                        </div>
                    </div>

                    <div class="support-box">
                        <h3>24/7 Customer Support</h3>
                        <p>Need immediate assistance? Our customer support team is available around the clock for urgent booking issues.</p>
                        <a href="https://wa.me/+97142296659?text=Hello%2C%20I%20need%20support" target="_blank" rel="noopener" class="support-link">
                            <button class="btn-support" type="button">Chat with Support</button>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="faq-section">
        <div class="container">
            <div class="faq-header">
                <h2 class="faq-title">Frequently Asked Questions</h2>
                <p class="faq-subtitle">Find quick answers to common questions.</p>
            </div>

            <div class="faq-grid">
                <div class="faq-item">
                    <h3 class="faq-question">How do I modify my booking?</h3>
                    <p class="faq-answer">Log into your account, open your bookings, and select the reservation you want to review or update. Changes depend on availability and supplier policy.</p>
                </div>
                <div class="faq-item">
                    <h3 class="faq-question">What's your cancellation policy?</h3>
                    <p class="faq-answer">Cancellation policies vary by property and booking type. Please check your booking confirmation for exact terms.</p>
                </div>
                <div class="faq-item">
                    <h3 class="faq-question">Do you offer group discounts?</h3>
                    <p class="faq-answer">Yes. For larger group requests, contact our team and we will guide you through special rates and support options.</p>
                </div>
                <div class="faq-item">
                    <h3 class="faq-question">Is my payment information secure?</h3>
                    <p class="faq-answer">Yes. We follow standard security practices to help protect your payment and personal information during booking.</p>
                </div>
            </div>
        </div>
    </section>
</div>

<style>
    .contact-us-page {
        font-family: "Inter", sans-serif;
    }

    .contact-hero {
        background: linear-gradient(180deg, rgba(5, 168, 199, 0.22) 0%, #ffffff 55%, rgba(241, 245, 249, 0.12) 100%);
        padding: 80px 20px;
        text-align: center;
    }

    .contact-hero-title {
        margin: 0 0 16px;
        color: #1d2025;
        font-weight: 800;
        font-size: 48px;
        line-height: 1.05;
    }

    .contact-hero-subtitle {
        font-size: 18px;
        color: #65758b;
        max-width: 700px;
        margin: 0 auto;
        line-height: 1.7;
    }

    .contact-main-section {
        padding: 60px 20px;
        background: #fff;
    }

    .contact-content-wrapper {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 40px;
        max-width: 1200px;
        margin: 0 auto;
    }

    .contact-form-container,
    .contact-info-container,
    .faq-item {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 2px 4px 0 rgba(0, 0, 0, 0.10);
        border: 1px solid rgba(225, 231, 239, 0.5);
    }

    .contact-form-container,
    .contact-info-container {
        padding: 40px;
    }

    .form-title {
        color: #1d2025;
        margin: 0 0 30px;
        font-weight: 700;
        font-size: 24px;
        line-height: 1.35;
    }

    .contact-form {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
    }

    .form-group {
        display: flex;
        flex-direction: column;
    }

    .form-group label {
        color: #1d2025;
        margin-bottom: 8px;
        font-weight: 500;
        font-size: 14px;
    }

    .required {
        color: #e63946;
    }

    .form-group input,
    .form-group textarea {
        padding: 12px 16px;
        border: 1px solid #ddd;
        border-radius: 6px;
        font-size: 14px;
        font-family: inherit;
        transition: border-color 0.3s ease, box-shadow 0.3s ease;
    }

    .form-group input:focus,
    .form-group textarea:focus {
        outline: none;
        border-color: #00acc1;
        box-shadow: 0 0 0 3px rgba(23, 195, 206, 0.12);
    }

    .form-group textarea {
        resize: vertical;
        min-height: 120px;
    }

    .form-submit-wrapper {
        display: flex;
        justify-content: flex-end;
    }

    .btn-submit {
        background: #05a8c7;
        color: #fff;
        padding: 14px 32px;
        border: none;
        border-radius: 10px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.3s ease;
        line-height: 20px;
    }

    .btn-submit:hover {
        background: #008c9e;
    }

    .form-mess {
        font-size: 14px;
        line-height: 1.5;
    }

    .form-mess.is-success {
        border: 1px solid #28a745;
        border-radius: 8px;
        padding: 12px 14px;
        color: #28a745;
        background: rgba(40, 167, 69, 0.05);
    }

    .contact-info-container {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    .contact-info-item {
        display: flex;
        gap: 16px;
    }

    .info-icon {
        flex-shrink: 0;
        width: 40px;
        height: 40px;
        background: #e0f7fa;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #00acc1;
    }

    .info-content h3 {
        color: #1d2025;
        margin: 0 0 8px;
        font-weight: 700;
        font-size: 16px;
        line-height: 24px;
    }

    .info-content p {
        color: #65758b;
        margin: 0;
        font-size: 16px;
        line-height: 24px;
    }

    .info-content p + p {
        margin-top: 4px;
    }

    .address-map-link {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        color: #05a8c7;
        text-decoration: none;
        white-space: nowrap;
        margin-top: 6px;
    }

    .address-map-text {
        font-weight: 700;
    }

    .support-box {
        background: rgba(5, 168, 199, 0.05);
        padding: 24px;
        border-radius: 12px;
        margin-top: 10px;
        text-align: left;
        border: 1px solid rgba(225, 231, 239, 0.5);
    }

    .support-box h3 {
        color: #1d2025;
        margin: 0 0 12px;
        font-weight: 700;
        font-size: 16px;
        line-height: 24px;
    }

    .support-box p {
        color: #65758b;
        margin: 0 0 16px;
        font-size: 14px;
        line-height: 20px;
    }

    .support-link {
        text-decoration: none;
    }

    .btn-support {
        background: #ffffff;
        color: #1d2025;
        padding: 12px 24px;
        border: 1px solid #e1e7ef;
        border-radius: 10px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.3s ease, color 0.3s ease;
        width: 100%;
        display: block;
        line-height: 20px;
    }

    .btn-support:hover {
        background: #008c9e;
        color: #fff;
    }

    .faq-section {
        padding: 60px 20px;
        background: rgba(241, 245, 249, 0.3);
    }

    .faq-header {
        text-align: center;
        margin-bottom: 50px;
    }

    .faq-title {
        font-size: 30px;
        font-weight: 700;
        color: #1d2025;
        margin: 0 0 12px;
        line-height: 36px;
    }

    .faq-subtitle {
        color: #65758b;
        margin: 0;
        font-size: 16px;
        line-height: 24px;
    }

    .faq-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 30px;
        max-width: 1200px;
        margin: 0 auto;
    }

    .faq-item {
        padding: 24px;
    }

    .faq-question {
        font-size: 16px;
        font-weight: 700;
        color: #1d2025;
        margin: 0 0 12px;
        line-height: 24px;
    }

    .faq-answer {
        font-size: 14px;
        color: #65758b;
        margin: 0;
        line-height: 1.7;
    }

    @media (max-width: 991.98px) {
        .contact-content-wrapper,
        .faq-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 767.98px) {
        .contact-hero {
            padding: 60px 16px;
        }

        .contact-main-section,
        .faq-section {
            padding: 40px 16px;
        }

        .contact-hero-title {
            font-size: 34px;
        }

        .contact-hero-subtitle {
            font-size: 16px;
        }

        .contact-form-container,
        .contact-info-container {
            padding: 24px 20px;
        }

        .form-row {
            grid-template-columns: 1fr;
        }

        .form-submit-wrapper {
            justify-content: stretch;
        }

        .btn-submit {
            width: 100%;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var form = document.getElementById('contactForm');
        var message = document.getElementById('formMessage');

        if (!form || !message) return;

        form.addEventListener('submit', function (event) {
            event.preventDefault();
            message.className = 'form-mess is-success';
            message.textContent = 'Thank you for your message. We will get back to you soon.';
            form.reset();
        });
    });
</script>
@endsection
