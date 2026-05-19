<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>PWESTO! - Reserve Your Space, Work Your Way</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: 'Inter', Arial, sans-serif;
            background: #f3f6fb;
            color: #111827;
        }
        .page {
            width: 100%;
            max-width: none;
            margin: 0;
            padding: 18px 18px 0;
        }
        .topbar {
            display: flex;
            align-items: center;
            justify-content: flex-start;
            padding: 8px 0 18px;
        }
        .brand {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            font-weight: 800;
            font-size: 30px;
            color: #0ea5a0;
            letter-spacing: 1px;
        }
        .brand-dot {
            width: 12px;
            height: 12px;
            border-radius: 999px;
            background: #19c2b8;
            box-shadow: 0 0 0 6px rgba(25, 194, 184, 0.2);
        }

        .hero {
            background: #fff;
            border: 1px solid #e8edf3;
            border-radius: 26px;
            box-shadow: 0 12px 28px rgba(2, 6, 23, 0.07);
            overflow: hidden;
            margin-bottom: 24px;
        }
        .hero-grid {
            display: grid;
            grid-template-columns: 0.95fr 1.05fr;
            align-items: stretch;
        }
        .hero-copy {
            padding: 54px 48px;
        }
        .hero-copy h1 {
            margin: 0 0 14px;
            font-size: clamp(2.5rem, 5.4vw, 4.2rem);
            line-height: 1.05;
            letter-spacing: -0.5px;
            font-weight: 900;
            color: #111827;
        }
        .hero-copy .accent {
            color: #19c2b8;
        }
        .hero-copy p {
            margin: 0 0 26px;
            color: #4b5563;
            max-width: 430px;
            line-height: 1.7;
            font-size: 1.1rem;
        }
        .hero-copy .cta {
            display: inline-block;
            background: linear-gradient(135deg, #0f172a, #111827);
            color: #fff;
            padding: 14px 34px;
            border-radius: 999px;
            font-weight: 800;
            text-decoration: none;
            font-size: 1.05rem;
            letter-spacing: 0.2px;
            box-shadow: 0 10px 18px rgba(15, 23, 42, 0.25);
            transition: transform 0.2s ease, box-shadow 0.2s ease, background 0.2s ease;
        }
        .hero-copy .cta:hover {
            transform: translateY(-2px);
            box-shadow: 0 14px 24px rgba(15, 23, 42, 0.32);
            background: linear-gradient(135deg, #111827, #0b1220);
        }
        .hero-art {
            position: relative;
            min-height: 560px;
            background:
                linear-gradient(180deg, rgba(255,255,255,0.05), rgba(255,255,255,0.15)),
                url('{{ asset('images/collab.jpg') }}') center/cover no-repeat;
        }
        .hero-art::before {
            content: "";
            position: absolute;
            inset: 0;
            background:
                linear-gradient(90deg, rgba(255,255,255,0.55) 0%, rgba(255,255,255,0.12) 100%),
                repeating-linear-gradient(
                    90deg,
                    transparent 0,
                    transparent 92px,
                    rgba(17, 24, 39, 0.18) 92px,
                    rgba(17, 24, 39, 0.18) 94px
                );
        }
        .hero-card {
            position: absolute;
            right: 34px;
            bottom: 30px;
            z-index: 2;
            background: rgba(255,255,255,0.9);
            backdrop-filter: blur(2px);
            border-radius: 16px;
            border: 1px solid #e5e7eb;
            padding: 12px;
            width: 220px;
        }
        .hero-card img {
            width: 100%;
            height: 120px;
            object-fit: cover;
            border-radius: 10px;
            display: block;
        }

        .partners {
            background: #fff;
            border: 1px solid #e8edf3;
            border-radius: 18px;
            padding: 18px;
            margin-bottom: 20px;
            display: flex;
            justify-content: center;
            gap: 18px;
            flex-wrap: wrap;
        }
        .partner {
            width: 110px;
            height: 90px;
            border-radius: 14px;
            border: 1px solid #e7eaf0;
            background: #f9fafb;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 10px;
        }
        .partner img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }

        .public-reviews-section {
            margin-bottom: 20px;
        }

        .public-reviews-wrap {
            background: #fff;
            border: 1px solid #e8edf3;
            border-radius: 18px;
            box-shadow: 0 8px 22px rgba(2, 6, 23, 0.06);
            padding: 2rem 1.5rem;
        }

        .public-reviews-title {
            text-align: center;
            margin: 0 0 0.5rem;
            color: #111827;
            font-size: clamp(1.5rem, 3vw, 2rem);
            font-weight: 800;
        }

        .public-reviews-sub {
            text-align: center;
            color: #6b7280;
            margin: 0 0 1.5rem;
            font-size: 1rem;
            line-height: 1.5;
        }

        .public-reviews-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.25rem;
        }

        .workspace-review-card {
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 1.1rem;
            background: #f9fafb;
        }

        .workspace-review-title {
            font-size: 1.05rem;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 0.35rem;
        }

        .workspace-review-meta {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 0.4rem;
            font-size: 0.85rem;
            color: #4b5563;
            margin-bottom: 0.8rem;
        }

        .review-item {
            border-top: 1px dashed #d1d5db;
            padding-top: 0.7rem;
            margin-top: 0.7rem;
        }

        .review-item:first-child {
            border-top: none;
            padding-top: 0;
            margin-top: 0;
        }

        .review-rating {
            display: flex;
            align-items: center;
            gap: 0.25rem;
            margin-bottom: 0.2rem;
        }

        .review-comment {
            font-size: 0.9rem;
            color: #374151;
            line-height: 1.4;
            margin-top: 0.25rem;
        }

        .review-footer {
            font-size: 0.78rem;
            color: #6b7280;
            margin-top: 0.3rem;
        }

        .public-reviews-empty {
            grid-column: 1 / -1;
            text-align: center;
            color: #6b7280;
            padding: 1.5rem 0;
        }

        .public-reviews-more {
            text-align: center;
            margin: 1.25rem 0 0;
        }

        .public-reviews-more a {
            color: #19c2b8;
            font-weight: 700;
            text-decoration: none;
            font-size: 0.95rem;
        }

        .public-reviews-more a:hover {
            text-decoration: underline;
        }

        .footer {
            background: #0f1115;
            color: #c9d1dd;
            border-radius: 18px 18px 0 0;
            padding: 28px 28px 18px;
            margin-top: 10px;
        }
        .footer-grid {
            display: grid;
            grid-template-columns: repeat(5, minmax(0, 1fr));
            gap: 22px;
        }
        .footer h4 {
            margin: 0 0 12px;
            color: #fff;
            font-size: 0.85rem;
            letter-spacing: 0.4px;
        }
        .footer p {
            margin: 0 0 6px;
            font-size: 0.88rem;
            line-height: 1.35;
        }
        .footer-brand {
            margin-top: 18px;
            color: #fff;
            font-weight: 700;
            font-size: 1rem;
        }
        @media (max-width: 980px) {
            .hero-grid { grid-template-columns: 1fr; }
            .hero-art { min-height: 300px; }
            .hero-copy { padding: 38px 28px 24px; }
            .hero-card { display: none; }
            .footer-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        }
    </style>
</head>
<body>
    <div class="page">
        <header class="topbar">
            <div class="brand">
                <span class="brand-dot"></span>
                PWESTO
            </div>
        </header>

        <section class="hero">
            <div class="hero-grid">
                <div class="hero-copy">
                    <h1>Find a Perfect <span class="accent">Working Space</span> Near You!</h1>
                    <p>
                        Discover flexible, inspiring coworking spaces built for solo work and team collaboration.
                        Book in minutes and work your way.
                    </p>
                    <a href="{{ route('login') }}" class="cta">Book Now</a>
                </div>
                <div class="hero-art">
                    <div class="hero-card">
                        <img src="{{ asset('images/nest 1.webp') }}" alt="Workspace scene">
                    </div>
                </div>
            </div>
        </section>

        @if(($reviewsByWorkspace ?? collect())->isNotEmpty())
        <section class="public-reviews-section" aria-label="Public reviews">
            @include('partials.public-workspace-reviews')
        </section>
        @endif

        <section class="partners">
            <div class="partner"><img src="{{ asset('images/produktiv.png') }}" alt="Produktiv"></div>
            <div class="partner"><img src="{{ asset('images/Nest.png') }}" alt="Nest"></div>
            <div class="partner"><img src="{{ asset('images/media.jpg') }}" alt="Mesh Media"></div>
        </section>

        <footer class="footer">
            <div class="footer-grid">
                <div>
                    <h4>FEATURE</h4>
                    <p>Team Management</p>
                    <p>Tasks Schedule</p>
                    <p>File Manager</p>
                </div>
                <div>
                    <h4>RESOURCES</h4>
                    <p>Blog</p>
                    <p>Support</p>
                    <p>Newsletter</p>
                </div>
                <div>
                    <h4>COMMUNITY</h4>
                    <p>Twitter</p>
                    <p>Instagram</p>
                    <p>Facebook</p>
                    <p>YouTube</p>
                </div>
                <div>
                    <h4>SUPPORT</h4>
                    <p>My Account</p>
                    <p>Help & Support</p>
                    <p>Contact Us</p>
                </div>
                <div>
                    <h4>COMPANY</h4>
                    <p>Privacy Policy</p>
                    <p>Terms of Service</p>
                    <p>Code of Conduct</p>
                </div>
            </div>
            <div class="footer-brand">Pwesto</div>
        </footer>
    </div>
</body>
</html>
