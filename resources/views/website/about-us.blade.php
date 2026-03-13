@extends('layouts.master')

@section('title', 'About Us')

@section('content')
<div class="about-us-page">
    <section class="about-hero">
        <div class="container">
            <h1 class="about-title">About Travolyo</h1>
            <p class="about-subtitle">
                Your trusted partner in creating unforgettable travel experiences. We connect millions
                of travelers with the world's best accommodations, flights, and activities.
            </p>
        </div>
    </section>

    <section class="our-mission">
        <div class="container">
            <h2 class="section-title">Our Mission</h2>
            <p class="section-description">
                To make travel accessible, affordable, and enjoyable for everyone, everywhere.
            </p>

            <div class="mission-cards">
                <div class="mission-card">
                    <div class="mission-icon">
                        <svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M16 29.3332C23.3638 29.3332 29.3333 23.3636 29.3333 15.9998C29.3333 8.63604 23.3638 2.6665 16 2.6665C8.63616 2.6665 2.66663 8.63604 2.66663 15.9998C2.66663 23.3636 8.63616 29.3332 16 29.3332Z" stroke="#05A8C7" stroke-width="2.66667" stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M16 2.6665C12.5763 6.26138 10.6666 11.0355 10.6666 15.9998C10.6666 20.9642 12.5763 25.7383 16 29.3332C19.4236 25.7383 21.3333 20.9642 21.3333 15.9998C21.3333 11.0355 19.4236 6.26138 16 2.6665Z" stroke="#05A8C7" stroke-width="2.66667" stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M2.66663 16H29.3333" stroke="#05A8C7" stroke-width="2.66667" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </div>
                    <h3>Global Reach</h3>
                    <p>Access to millions of properties and activities in over 200 countries worldwide.</p>
                </div>

                <div class="mission-card">
                    <div class="mission-icon">
                        <svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M26.6667 17.3333C26.6667 23.9999 22 27.3333 16.4534 29.2666C16.1629 29.365 15.8474 29.3603 15.56 29.2533C10 27.3333 5.33337 23.9999 5.33337 17.3333V7.99995C5.33337 7.64633 5.47385 7.30719 5.7239 7.05714C5.97395 6.80709 6.31309 6.66662 6.66671 6.66662C9.33337 6.66662 12.6667 5.06662 14.9867 3.03995C15.2692 2.79861 15.6285 2.66602 16 2.66602C16.3716 2.66602 16.7309 2.79861 17.0134 3.03995C19.3467 5.07995 22.6667 6.66662 25.3334 6.66662C25.687 6.66662 26.0261 6.80709 26.2762 7.05714C26.5262 7.30719 26.6667 7.64633 26.6667 7.99995V17.3333Z" stroke="#05A8C7" stroke-width="2.66667" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </div>
                    <h3>Trusted Service</h3>
                    <p>Secure bookings with 24/7 customer support and a best price guarantee.</p>
                </div>

                <div class="mission-card">
                    <div class="mission-icon">
                        <svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M25.3333 18.6667C27.32 16.72 29.3333 14.3867 29.3333 11.3333C29.3333 9.38841 28.5607 7.52315 27.1854 6.14788C25.8101 4.77262 23.9449 4 22 4C19.6533 4 18 4.66667 16 6.66667C14 4.66667 12.3466 4 9.99996 4C8.05504 4 6.18978 4.77262 4.81451 6.14788C3.43924 7.52315 2.66663 9.38841 2.66663 11.3333C2.66663 14.4 4.66663 16.7333 6.66663 18.6667L16 28L25.3333 18.6667Z" stroke="#05A8C7" stroke-width="2.66667" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </div>
                    <h3>Customer First</h3>
                    <p>Millions of verified reviews from real travelers to help you book with confidence.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="our-impact">
        <div class="container">
            <h2 class="section-title">Our Impact</h2>

            <div class="impact-stats">
                <div class="stat-item">
                    <div class="stat-number">50M+</div>
                    <div class="stat-label">Happy Travelers</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">2M+</div>
                    <div class="stat-label">Properties Listed</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">200+</div>
                    <div class="stat-label">Countries Covered</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">24/7</div>
                    <div class="stat-label">Customer Support</div>
                </div>
            </div>
        </div>
    </section>

    <section class="our-values">
        <div class="container">
            <h2 class="section-title">Our Values</h2>

            <div class="values-grid">
                <div class="value-card">
                    <div class="value-icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M16 21V19C16 17.9391 15.5786 16.9217 14.8284 16.1716C14.0783 15.4214 13.0609 15 12 15H6C4.93913 15 3.92172 15.4214 3.17157 16.1716C2.42143 16.9217 2 17.9391 2 19V21" stroke="#05A8C7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M9 11C11.2091 11 13 9.20914 13 7C13 4.79086 11.2091 3 9 3C6.79086 3 5 4.79086 5 7C5 9.20914 6.79086 11 9 11Z" stroke="#05A8C7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M22 20.9999V18.9999C21.9993 18.1136 21.7044 17.2527 21.1614 16.5522C20.6184 15.8517 19.8581 15.3515 19 15.1299" stroke="#05A8C7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M16 3.12988C16.8604 3.35018 17.623 3.85058 18.1676 4.55219C18.7122 5.2538 19.0078 6.11671 19.0078 7.00488C19.0078 7.89305 18.7122 8.75596 18.1676 9.45757C17.623 10.1592 16.8604 10.6596 16 10.8799" stroke="#05A8C7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </div>
                    <div class="value-content">
                        <h3>Community Focus</h3>
                        <p>Building a global community of travelers and hosts who share real experiences and recommendations.</p>
                    </div>
                </div>

                <div class="value-card">
                    <div class="value-icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M15.477 12.8901L16.992 21.4161C17.009 21.5165 16.9949 21.6197 16.9516 21.7119C16.9084 21.8041 16.838 21.8808 16.7499 21.9319C16.6619 21.983 16.5603 22.006 16.4588 21.9978C16.3573 21.9897 16.2607 21.9507 16.182 21.8861L12.602 19.1991C12.4292 19.07 12.2192 19.0003 12.0035 19.0003C11.7878 19.0003 11.5778 19.07 11.405 19.1991L7.819 21.8851C7.74032 21.9496 7.64386 21.9885 7.54249 21.9967C7.44112 22.0049 7.33967 21.982 7.25166 21.931C7.16365 21.88 7.09327 21.8035 7.04991 21.7115C7.00656 21.6195 6.99228 21.5165 7.009 21.4161L8.523 12.8901" stroke="#05A8C7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M12 14C15.3137 14 18 11.3137 18 8C18 4.68629 15.3137 2 12 2C8.68629 2 6 4.68629 6 8C6 11.3137 8.68629 14 12 14Z" stroke="#05A8C7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </div>
                    <div class="value-content">
                        <h3>Excellence</h3>
                        <p>Committed to providing exceptional service and a smoother travel booking experience at every step.</p>
                    </div>
                </div>

                <div class="value-card">
                    <div class="value-icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M22 7L13.5 15.5L8.5 10.5L2 17" stroke="#05A8C7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M16 7H22V13" stroke="#05A8C7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </div>
                    <div class="value-content">
                        <h3>Innovation</h3>
                        <p>Continuously improving our platform with practical technology that makes planning easier.</p>
                    </div>
                </div>

                <div class="value-card">
                    <div class="value-icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M20 13C20 18 16.5 20.5 12.34 21.95C12.1222 22.0238 11.8855 22.0202 11.67 21.94C7.5 20.5 4 18 4 13V5.99996C4 5.73474 4.10536 5.48039 4.29289 5.29285C4.48043 5.10532 4.73478 4.99996 5 4.99996C7 4.99996 9.5 3.79996 11.24 2.27996C11.4519 2.09896 11.7214 1.99951 12 1.99951C12.2786 1.99951 12.5481 2.09896 12.76 2.27996C14.51 3.80996 17 4.99996 19 4.99996C19.2652 4.99996 19.5196 5.10532 19.7071 5.29285C19.8946 5.48039 20 5.73474 20 5.99996V13Z" stroke="#05A8C7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </div>
                    <div class="value-content">
                        <h3>Trust & Safety</h3>
                        <p>Your security matters to us. We prioritize reliable systems and transparent booking experiences.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="our-story">
        <div class="container">
            <div class="story-content">
                <div class="story-image">
                    <img src="{{ asset('assets/images/website/about-us/our-story-img.jpg') }}" alt="Our Story">
                </div>
                <div class="story-text">
                    <h2 class="section-title">Our Story</h2>
                    <p>
                        Founded with a simple idea, Travolyo aims to make travel booking as easy as browsing your favorite website. What began as a focused concept has grown into a platform built to serve modern travelers with clarity and confidence.
                    </p>
                    <p>
                        We believe travel has the power to transform lives, broaden perspectives, and create lasting memories. That is why we work to remove the friction that often makes trip planning feel complicated and stressful.
                    </p>
                    <p>
                        Today, Travolyo brings hotels, flights, and experiences together in one place so travelers can discover better options, compare faster, and book with peace of mind.
                    </p>
                </div>
            </div>
        </div>
    </section>
</div>

<style>
    .about-us-page {
        font-family: "Inter", sans-serif;
        color: #1d2025;
    }

    .about-hero {
        background: linear-gradient(180deg, rgba(5, 168, 199, 0.22) 0%, #ffffff 55%, rgba(241, 245, 249, 0.12) 100%);
        padding: 100px 0 80px;
        text-align: center;
    }

    .about-title {
        margin-bottom: 20px;
        color: #1d2025;
        font-weight: 800;
        font-size: 48px;
        line-height: 1.05;
    }

    .about-subtitle {
        font-size: 18px;
        color: #65758b;
        max-width: 800px;
        margin: 0 auto;
        line-height: 1.7;
    }

    .about-us-page section {
        padding: 80px 0;
    }

    .section-title {
        text-align: center;
        margin-bottom: 20px;
        color: #1d2025;
        font-weight: 800;
        font-size: 30px;
        line-height: 1.2;
    }

    .section-description {
        font-size: 18px;
        text-align: center;
        color: #65758b;
        margin-bottom: 50px;
        line-height: 1.6;
    }

    .our-mission {
        background: #ffffff;
    }

    .mission-cards {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 30px;
        margin-top: 40px;
    }

    .mission-card {
        background: #ffffff;
        border-radius: 12px;
        padding: 40px 30px;
        text-align: center;
        box-shadow: 0 2px 4px 0 rgba(0, 0, 0, 0.10);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        border: 1px solid rgba(225, 231, 239, 0.5);
    }

    .mission-card:hover,
    .value-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 25px rgba(0, 0, 0, 0.08);
    }

    .mission-icon {
        width: 80px;
        height: 80px;
        background: rgba(5, 168, 199, 0.1);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 20px;
        color: #05A8C7;
    }

    .mission-card h3,
    .value-content h3 {
        font-size: 20px;
        font-weight: 700;
        margin-bottom: 12px;
        color: #1d2025;
    }

    .mission-card p,
    .value-content p,
    .story-text p {
        font-size: 16px;
        line-height: 1.7;
        color: #65758b;
        margin-bottom: 0;
    }

    .our-impact {
        background: rgba(241, 245, 249, 0.61);
    }

    .impact-stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 40px;
        margin-top: 50px;
    }

    .stat-item {
        text-align: center;
    }

    .stat-number {
        font-size: 36px;
        font-weight: 800;
        color: #f3c85b;
        margin-bottom: 10px;
        line-height: 1.1;
    }

    .stat-label {
        font-size: 16px;
        color: #65758b;
        letter-spacing: 0.5px;
    }

    .our-values {
        background: #ffffff;
    }

    .values-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 30px;
        margin-top: 50px;
    }

    .value-card {
        display: flex;
        align-items: flex-start;
        gap: 20px;
        background: #ffffff;
        border-radius: 14px;
        padding: 28px;
        transition: all 0.3s ease;
        border: 1px solid rgba(225, 231, 239, 0.5);
    }

    .value-icon {
        min-width: 60px;
        height: 60px;
        background: rgba(5, 168, 199, 0.1);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #05A8C7;
    }

    .our-story {
        background: rgba(241, 245, 249, 0.3);
    }

    .story-content {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 60px;
        align-items: center;
    }

    .story-image {
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    }

    .story-image img {
        width: 100%;
        height: 100%;
        display: block;
        object-fit: cover;
    }

    .story-text .section-title {
        text-align: left;
        margin-bottom: 25px;
    }

    .story-text p {
        margin-bottom: 18px;
    }

    @media (max-width: 991.98px) {
        .about-title {
            font-size: 40px;
        }

        .story-content,
        .values-grid {
            grid-template-columns: 1fr;
        }

        .story-text .section-title {
            text-align: center;
        }
    }

    @media (max-width: 767.98px) {
        .about-hero {
            padding: 60px 0 50px;
        }

        .about-title {
            font-size: 32px;
        }

        .about-subtitle,
        .section-description {
            font-size: 16px;
        }

        .about-us-page section {
            padding: 56px 0;
        }

        .section-title {
            font-size: 28px;
        }

        .mission-cards,
        .impact-stats,
        .values-grid {
            grid-template-columns: 1fr;
            gap: 20px;
        }

        .mission-card,
        .value-card {
            padding: 24px 20px;
        }
    }
</style>
@endsection
