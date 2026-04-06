@extends('layouts.master')

@section('title', 'Careers')

@section('canonical', 'https://www.travolyo.com/careers')

@section('content')
<div class="careers-page">
    <section class="careers-hero">
        <div class="container">
            <h1 class="careers-title">Careers at Travolyo</h1>
            <p class="careers-subtitle">Build meaningful travel experiences with a global, design driven travel platform.</p>
        </div>
    </section>

    <section class="careers-content">
        <div class="container">
            <div class="careers-card">
                <div class="careers-section">
                    <p>Travolyo is a growing travel technology company focused on presenting destinations and stays through thoughtful curation, strong visual storytelling, and modern digital experiences. Behind every experience is a team committed to quality, innovation, and long-term growth.</p>
                    <p>We are building Travolyo with purpose, and we are looking for people who want to grow with us.</p>
                </div>

                <div class="careers-section">
                    <h2>Life at Travolyo</h2>
                    <p>At Travolyo, collaboration and trust shape how we work. We value ownership, clear communication, and diverse perspectives. Teams are encouraged to think creatively, take responsibility, and contribute ideas that move the platform forward.</p>
                    <p>As a brand serving global travellers, we approach our work with curiosity, care, and attention to detail.</p>
                </div>

                <div class="careers-section">
                    <h2>What Makes Travolyo Different</h2>
                    <ul>
                        <li><strong>Meaningful Impact:</strong> Your work directly influences how travelers explore destinations and discover stays worldwide.</li>
                        <li><strong>Collaborative Environment:</strong> Work closely with teams across product, design, content, and growth.</li>
                        <li><strong>Room to Grow:</strong> As Travolyo expands, opportunities grow with it professionally and creatively.</li>
                        <li><strong>Global Perspective:</strong> Contribute to a platform built for international markets and diverse audiences.</li>
                    </ul>
                </div>

                <div class="careers-section">
                    <h2>Our Teams</h2>
                    <p>Travolyo brings together professionals across:</p>
                    <ul>
                        <li>Product and Technology</li>
                        <li>Brand, Content and Marketing</li>
                        <li>Partnerships and Business Development</li>
                        <li>Data and Analytics</li>
                        <li>Customer Experience and Operations</li>
                    </ul>
                    <p>Each team plays a vital role in shaping a reliable and inspiring travel platform.</p>
                </div>

                <div class="careers-section">
                    <h2>Open Opportunities</h2>
                    <p>Travolyo is continuously growing. While roles may change over time, we welcome applications from individuals who share our vision and values.</p>
                    <p><strong>Send your CV to:</strong> <a href="mailto:careers@travolyo.com">careers@travolyo.com</a></p>
                </div>

                <div class="careers-section">
                    <h2>Hiring Process</h2>
                    <p>Our hiring process is designed to be transparent and respectful:</p>
                    <ol>
                        <li>Application review</li>
                        <li>Initial conversation</li>
                        <li>Role-specific interview</li>
                        <li>Final discussion</li>
                    </ol>
                    <p>We aim to communicate clearly at every stage.</p>
                </div>
            </div>
        </div>
    </section>
</div>

<style>
    .careers-page {
        font-family: "Inter", sans-serif;
        color: #1d2025;
    }

    .careers-hero {
        background: linear-gradient(180deg, rgba(5, 168, 199, 0.22) 0%, #ffffff 55%, rgba(241, 245, 249, 0.12) 100%);
        padding: 100px 0 80px;
        text-align: center;
    }

    .careers-title {
        margin-bottom: 20px;
        color: #1d2025;
        font-weight: 800;
        font-size: 48px;
        line-height: 1.05;
    }

    .careers-subtitle {
        font-size: 18px;
        color: #65758b;
        max-width: 860px;
        margin: 0 auto;
        line-height: 1.7;
    }

    .careers-page section {
        padding: 70px 0;
    }

    .careers-content {
        background: #ffffff;
    }

    .careers-card {
        border: 1px solid #e6edf5;
        border-radius: 16px;
        padding: 36px 40px;
        background: #f9fbfe;
        box-shadow: 0 2px 10px rgba(16, 24, 40, 0.06);
    }

    .careers-section {
        margin-bottom: 28px;
    }

    .careers-section:last-child {
        margin-bottom: 0;
    }

    .careers-section h2 {
        font-size: 20px;
        font-weight: 700;
        margin-bottom: 10px;
        color: #1d2025;
    }

    .careers-section p,
    .careers-section li {
        margin-bottom: 10px;
        font-size: 15px;
        line-height: 26px;
        color: #5b667a;
    }

    .careers-section ul,
    .careers-section ol {
        margin: 0 0 10px 0;
        padding-left: 18px;
        color: #5b667a;
    }

    .careers-section a {
        color: #05a8c7;
        font-weight: 700;
        text-decoration: none;
    }

    .careers-section a:hover {
        text-decoration: underline;
    }

    @media (max-width: 768px) {
        .careers-hero {
            padding: 70px 0 60px;
        }

        .careers-title {
            font-size: 36px;
            line-height: 40px;
        }

        .careers-subtitle {
            font-size: 16px;
        }

        .careers-card {
            padding: 24px;
        }
    }
</style>
@endsection
