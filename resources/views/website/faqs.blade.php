@extends('layouts.master')

@section('title', 'FAQs')

@section('content')
@php
    $faq_groups = [
        [
            'title' => 'Platform, Flights & Stays',
            'items' => [
                [
                    'q' => 'How does Travolyo work?',
                    'a' => 'With Travolyo, you can search, compare, and book flights, hotels, apartments, and events online. Once your booking is complete, a confirmation is sent to your registered email.'
                ],
                [
                    'q' => 'Does Travolyo work with verified airlines, hotels, apartments, and event organizers?',
                    'a' => 'Yes. Travolyo partners with authorized airlines, trusted hotels, verified apartment providers, and event organizers to ensure reliable bookings.'
                ],
                [
                    'q' => 'Is Travolyo a safe platform for booking flights, hotels, apartments, and events?',
                    'a' => 'Yes. Travolyo uses secure systems and trusted payment gateways to protect your personal and payment information.'
                ],
                [
                    'q' => 'Can I book flights on Travolyo?',
                    'a' => 'Yes. On Travolyo, you can search and book both domestic and international flights through multiple airline partners.'
                ],
                [
                    'q' => 'Are flight prices on Travolyo final?',
                    'a' => 'Flight prices on Travolyo include all applicable taxes and fees and are confirmed before payment.'
                ],
                [
                    'q' => 'Will I get my flight ticket immediately after booking on Travolyo?',
                    'a' => 'In most cases, flight tickets on Travolyo are issued shortly after payment confirmation and sent directly to your email.'
                ],
                [
                    'q' => 'Can I manage my flight booking after purchase on Travolyo?',
                    'a' => 'Yes. After booking on Travolyo, airline reference details are provided so you can manage your booking directly with the airline where applicable.'
                ],
                [
                    'q' => 'Can I book hotels on Travolyo?',
                    'a' => 'Yes. On Travolyo, hotel bookings are available across multiple destinations, covering a variety of budgets and stay options.'
                ],
                [
                    'q' => 'Are hotels on Travolyo verified?',
                    'a' => 'Yes. Hotels on Travolyo are sourced from trusted partners and global suppliers to ensure reliability.'
                ],
                [
                    'q' => 'Can I book apartments on Travolyo?',
                    'a' => 'Yes. Travolyo offers apartment booking options in multiple cities for short-term and long-term stays.'
                ],
                [
                    'q' => 'Will I receive booking confirmation instantly for hotels or apartments on Travolyo?',
                    'a' => 'Most hotel and apartment bookings on Travolyo are confirmed instantly, with details sent to your email.'
                ],
                [
                    'q' => 'Are taxes and fees included in hotel and apartment prices on Travolyo?',
                    'a' => 'Yes. Prices on Travolyo generally include all applicable taxes and fees unless stated otherwise before checkout.'
                ],
                [
                    'q' => 'Can I book tickets for events on Travolyo?',
                    'a' => 'Yes. On Travolyo, you can book tickets for concerts, workshops, and other events through verified partners.'
                ],
            ],
        ],
        [
            'title' => 'Events, Payments, Refunds & Support',
            'items' => [
                [
                    'q' => 'Are events listed on Travolyo verified?',
                    'a' => 'Yes. Events on Travolyo are sourced from trusted and authorized organizers.'
                ],
                [
                    'q' => 'Will I receive confirmation instantly for event bookings on Travolyo?',
                    'a' => 'Yes. After successful payment on Travolyo, event booking confirmations are sent immediately to your email.'
                ],
                [
                    'q' => 'What payment methods does Travolyo accept?',
                    'a' => 'Travolyo supports secure online payment methods, with availability depending on your location and booking type.'
                ],
                [
                    'q' => 'Is my payment information secure on Travolyo?',
                    'a' => 'Yes. On Travolyo, all payment information is protected using encryption and trusted payment gateways.'
                ],
                [
                    'q' => 'Will I receive a payment receipt after booking on Travolyo?',
                    'a' => 'Yes. Once a transaction is completed on Travolyo, a payment confirmation and receipt are sent via email.'
                ],
                [
                    'q' => 'Can I cancel my booking on Travolyo?',
                    'a' => 'On Travolyo, cancellation eligibility depends on the airline, hotel, apartment, or event policy displayed before booking.'
                ],
                [
                    'q' => 'How do refunds work on Travolyo?',
                    'a' => 'Refunds on Travolyo are processed according to the supplier cancellation policy and issued to the original payment method.'
                ],
                [
                    'q' => 'How long does a Travolyo refund take?',
                    'a' => 'Refund timelines on Travolyo vary by supplier and bank and may take several business days to reflect in your account.'
                ],
                [
                    'q' => 'Are all bookings refundable on Travolyo?',
                    'a' => 'No. On Travolyo, some bookings are non-refundable. Always review cancellation and refund terms before confirming your booking.'
                ],
                [
                    'q' => 'Does Travolyo provide post-booking support?',
                    'a' => 'Yes. Travolyo offers support for booking confirmations, cancellations, refunds, and related issues.'
                ],
                [
                    'q' => 'How can I contact Travolyo customer support?',
                    'a' => 'Travolyo customer support can be reached through the official contact channels listed on the website.'
                ],
                [
                    'q' => 'What should I do if I face an issue with my Travolyo booking?',
                    'a' => 'For assistance, contact Travolyo customer support using the official channels on the website.'
                ],
            ],
        ],
    ];
@endphp

<div class="faqs-page">
    <section class="faq-hero">
        <div class="container">
            <h1 class="faq-hero-title">Travolyo, Frequently Asked Questions</h1>
            <p class="faq-hero-subtitle">Travolyo offers seamless booking for flights, hotels, apartments, and events. Find answers to common questions below to help you book with confidence.</p>
        </div>
    </section>

    <section class="faq-content">
        <div class="container">
            <div class="faq-category">
                <h2 class="faq-category-title">General FAQs</h2>
                <div class="faq-groups">
                    @foreach ($faq_groups as $group)
                        <div class="faq-group">
                            <h3 class="faq-group-title">{{ $group['title'] }}</h3>
                            <div class="faq-items">
                                @foreach ($group['items'] as $item)
                                    @php $faq_id = 'faq-' . $loop->parent->index . '-' . $loop->index; @endphp
                                    <div class="faq-item" data-faq-item>
                                        <button class="faq-question" type="button" aria-expanded="false" aria-controls="{{ $faq_id }}">
                                            <span>{{ $item['q'] }}</span>
                                            <span class="faq-arrow" aria-hidden="true"></span>
                                        </button>
                                        <div class="faq-answer" id="{{ $faq_id }}" role="region" aria-hidden="true">
                                            <p>{{ $item['a'] }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>
</div>

<style>
    .faqs-page {
        font-family: "Inter", sans-serif;
        color: #1d2025;
    }

    .faq-hero {
        background: linear-gradient(180deg, rgba(5, 168, 199, 0.22) 0%, #ffffff 55%, rgba(241, 245, 249, 0.12) 100%);
        padding: 100px 0 80px;
        text-align: center;
    }

    .faq-hero-title {
        margin-bottom: 20px;
        color: #1d2025;
        font-weight: 800;
        font-size: 48px;
        line-height: 1.05;
    }

    .faq-hero-subtitle {
        font-size: 18px;
        color: #65758b;
        max-width: 820px;
        margin: 0 auto;
        line-height: 1.7;
    }

    .faqs-page section {
        padding: 70px 0;
    }

    .faq-content {
        background: #ffffff;
    }

    .faq-category {
        margin-bottom: 60px;
    }

    .faq-category:last-child {
        margin-bottom: 0;
    }

    .faq-category-title {
        font-size: 28px;
        font-weight: 700;
        margin-bottom: 20px;
        color: #1d2025;
    }

    .faq-groups {
        display: grid;
        gap: 24px;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        align-items: start;
    }

    .faq-group {
        border: 1px solid #e6edf5;
        border-radius: 14px;
        padding: 24px 28px;
        background: #f9fbfe;
        box-shadow: 0 2px 8px rgba(16, 24, 40, 0.06);
    }

    .faq-group-title {
        font-size: 20px;
        font-weight: 700;
        margin-bottom: 18px;
        color: #1d2025;
    }

    .faq-items {
        display: grid;
        grid-template-columns: 1fr;
    }

    .faq-item {
        padding: 16px 0;
        border-top: 1px solid rgba(15, 23, 42, 0.08);
    }

    .faq-item:first-child {
        border-top: none;
        padding-top: 0;
    }

    .faq-question {
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        background: transparent;
        border: none;
        padding: 0;
        cursor: pointer;
        text-align: left;
        font-size: 16px;
        font-weight: 600;
        color: #1d2025;
    }

    .faq-answer {
        max-height: 0;
        overflow: hidden;
        opacity: 0;
        transition: max-height 0.35s ease, opacity 0.2s ease;
        color: #5b667a;
        font-size: 15px;
        line-height: 24px;
    }

    .faq-answer p {
        margin: 0;
        padding-top: 10px;
    }

    .faq-arrow {
        width: 12px;
        height: 12px;
        border-right: 2px solid #05a8c7;
        border-bottom: 2px solid #05a8c7;
        transform: rotate(45deg);
        transition: transform 0.2s ease;
        flex-shrink: 0;
        margin-top: 2px;
    }

    .faq-item.is-open .faq-arrow {
        transform: rotate(-135deg);
    }

    .faq-item.is-open .faq-answer {
        opacity: 1;
    }

    .faq-question:focus,
    .faq-question:focus-visible {
        outline: none;
        box-shadow: none;
    }

    @media (max-width: 768px) {
        .faq-hero {
            padding: 70px 0 60px;
        }

        .faq-hero-title {
            font-size: 36px;
            line-height: 40px;
        }

        .faq-hero-subtitle {
            font-size: 16px;
        }

        .faq-category-title {
            font-size: 24px;
        }

        .faq-group {
            padding: 20px;
        }

        .faq-groups {
            grid-template-columns: 1fr;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var items = document.querySelectorAll('.faq-item[data-faq-item]');
        if (!items.length) return;

        items.forEach(function (item) {
            var button = item.querySelector('.faq-question');
            var panel = item.querySelector('.faq-answer');
            if (!button || !panel) return;

            button.setAttribute('aria-expanded', 'false');
            panel.setAttribute('aria-hidden', 'true');
            panel.style.maxHeight = '0px';

            button.addEventListener('click', function () {
                var isOpen = button.getAttribute('aria-expanded') === 'true';

                if (isOpen) {
                    button.setAttribute('aria-expanded', 'false');
                    panel.setAttribute('aria-hidden', 'true');
                    panel.style.maxHeight = '0px';
                    item.classList.remove('is-open');
                    return;
                }

                button.setAttribute('aria-expanded', 'true');
                panel.setAttribute('aria-hidden', 'false');
                item.classList.add('is-open');
                panel.style.maxHeight = panel.scrollHeight + 'px';
            });
        });
    });
</script>
@endsection
