<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>PWESTO! - Reserve Your Space, Work Your Way</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <style>
        body { font-family: Arial, sans-serif; background: #f5faff; margin: 0; }
        .container { max-width: 1200px; margin: auto; padding: 2rem; }
        .header, .footer { text-align: center; }
        .features, .testimonials, .blog, .collab { margin: 3rem 0; }
        .features-list, .testimonials-list, .blog-list, .collab-list { display: flex; gap: 2rem; flex-wrap: wrap; justify-content: center; }
        .card { background: #fff; border-radius: 10px; box-shadow: 0 2px 8px #0001; padding: 1.5rem; flex: 1 1 250px; min-width: 250px; }
        .cta { background: #fff; padding: 2rem; border-radius: 10px; text-align: center; margin: 2rem 0; }
        .cta button { background: #1abc9c; color: #fff; border: none; padding: 1rem 2rem; border-radius: 5px; font-size: 1.2rem; cursor: pointer; }
        .footer { background: #222; color: #fff; padding: 2rem 0; }
        .footer a { color: #fff; margin: 0 1rem; text-decoration: none; }
        .logo { font-size: 2.5rem; font-weight: bold; color: #009688; }
        .tagline { color: #009688; font-size: 1.2rem; }
        .section-title { font-size: 2rem; font-weight: bold; margin-bottom: 1.5rem; text-align: center; }
        .section-subtitle { color: #555; text-align: center; margin-bottom: 2rem; }
    </style>
</head>
<body>
    <div class="container">
        <!-- Hero/Header -->
        <div style="width:100%;text-align:center;margin-top:2.5rem;margin-bottom:3rem;">
            <div style="font-size:3.5rem;font-weight:900;color:#19c2b8;letter-spacing:3px;line-height:1;">
                PWESTO!
            </div>
            <div style="font-size:2rem;font-weight:600;color:#222;margin-top:1rem;">
                Reserve Your Space, Work Your Way
            </div>
        </div>

        <!-- Features/Intro -->
        <div style="display: flex; align-items: center; justify-content: center; gap: 5rem; margin: 5rem 0;">
            <!-- Images Section -->
            <div style="position: relative; width: 540px; min-width: 400px; height: 440px;">
                <img src="{{ asset('images/worker1.jpg') }}" alt="Main" style="width: 420px; height: 440px; object-fit: cover; border-radius: 32px; box-shadow: 0 6px 32px #0002;">
                <img src="{{ asset('images/worker2.jpg') }}" alt="Overlay" style="position: absolute; top: 40px; left: -80px; width: 200px; height: 220px; object-fit: cover; border-radius: 24px; border: 10px solid #fff; box-shadow: 0 6px 24px #0002;">
            </div>

            <!-- Text and Features Section -->
            <div>
                <h2 style="font-size: 2.7rem; font-weight: 800; margin-bottom: 1.2rem; color: #222; line-height: 1.2;">
                    Best creative working environments that suits your business.
                </h2>
                <p style="color: #555; margin-bottom: 2.7rem; font-size: 1.2rem;">
                Experience a professional and inspiring workspace designed to support productivity, innovation, and growth. Our facilities cater to individuals and teams seeking a flexible and efficient working environment.
                </p>
                <div style="display: flex; flex-direction: column; gap: 2rem;">
                    <div style="display: flex; align-items: center; gap: 1.5rem;">
                        <span style="background: #22c55e; color: #fff; border-radius: 14px; padding: 0.9rem; display: flex; align-items: center;">
                            <!-- Example icon: clock -->
                            <svg width="32" height="32" fill="none" stroke="currentColor" stroke-width="2"><circle cx="16" cy="16" r="14"/><path d="M16 8v9l6 2"/></svg>
                        </span>
                        <div>
                            <div style="font-weight: 700; font-size: 1.3rem;">Open 24 hours</div>
                            <div style="color: #555; font-size: 1.05rem;">Access your workspace at any time. There facility remains open 24/7 to accommodate your unique schedule and working preferences.</div>
                        </div>
                    </div>
                    <div style="display: flex; align-items: center; gap: 1.5rem;">
                        <span style="background: #22c55e; color: #fff; border-radius: 14px; padding: 0.9rem; display: flex; align-items: center;">
                            <!-- Example icon: cup -->
                            <svg width="32" height="32" fill="none" stroke="currentColor" stroke-width="2"><path d="M7 15h18v2a9 9 0 01-18 0v-2z"/><path d="M25 15V9a2 2 0 00-2-2H9a2 2 0 00-2 2v6"/></svg>
                        </span>
                        <div>
                            <div style="font-weight: 700; font-size: 1.3rem;">Services Coffee, drink and snacks</div>
                            <div style="color: #555; font-size: 1.05rem;">Stay refreshed and focused with There curated selection of complimentary coffee, beverages, and light snacks—designed to keep you energized and inspired throughout your workday.</div>
                        </div>
                    </div>
                    <div style="display: flex; align-items: center; gap: 1.5rem;">
                        <span style="background: #22c55e; color: #fff; border-radius: 14px; padding: 0.9rem; display: flex; align-items: center;">
                            <!-- Example icon: game controller -->
                            <svg width="32" height="32" fill="none" stroke="currentColor" stroke-width="2"><rect x="4" y="10" width="24" height="12" rx="8"/><path d="M10 16h.01"/><path d="M22 16h.01"/><path d="M16 20v.01"/><path d="M16 12v.01"/></svg>
                        </span>
                        <div>
                            <div style="font-weight: 700; font-size: 1.3rem;">Services Equipment</div>
                            <div style="color: #555; font-size: 1.05rem;">They provide essential tech accessories on loan to ensure your workflow remains uninterrupted.</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Call to Action -->
        <div class="cta">
            <h2>The Easiest Way to Book Your Workspace</h2>
            <a href="{{ route('login') }}" style="display:inline-block; background:#22c55e; color:#fff; border:none; padding:1rem 2.5rem; border-radius:8px; font-size:1.2rem; font-weight:600; text-decoration:none; margin-top:1rem; transition:background 0.2s;">BOOK NOW</a>
        </div>

        <!-- Testimonials -->
        <div class="testimonials">
            <div class="section-title">They Satisfied With Our PWESTO</div>
            <div class="testimonials-list">
                <div class="card">
                    <p>"Great place to work and collaborate!"</p>
                    <strong>John Doe</strong>
                    <div>⭐⭐⭐⭐⭐</div>
                </div>
                <div class="card">
                    <p>"Amazing amenities and friendly staff."</p>
                    <strong>Jane Smith</strong>
                    <div>⭐⭐⭐⭐⭐</div>
                </div>
                <div class="card">
                    <p>"Flexible booking and great atmosphere."</p>
                    <strong>Alex Lee</strong>
                    <div>⭐⭐⭐⭐⭐</div>
                </div>
            </div>
        </div>

        <!-- Blog Section -->
        <div class="blog">
            <div class="section-title">Our Latest Blog</div>
            <div class="blog-list">
                <div class="card">
                    <img src="{{ asset('images/blog1.jpg') }}" alt="Blog 1" style="width:100%; border-radius:8px;">
                    <h4>Why use a co-working space?</h4>
                    <p>Discover the benefits of flexible workspaces for your business.</p>
                </div>
                <div class="card">
                    <img src="{{ asset('images/blog2.jpg') }}" alt="Blog 2" style="width:100%; border-radius:8px;">
                    <h4>Easy Tips for First Timers</h4>
                    <p>How to get the most out of your first co-working experience.</p>
                </div>
                <div class="card">
                    <img src="{{ asset('images/blog3.jpg') }}" alt="Blog 3" style="width:100%; border-radius:8px;">
                    <h4>Creating a Creative Space</h4>
                    <p>How to design a workspace that inspires productivity.</p>
                </div>
            </div>
        </div>

        <!-- Company Collaboration -->
        <div style="position:relative; margin-top:4rem;">
            <div style="background:url('{{ asset('images/collab.jpg') }}') center/cover no-repeat; min-height:320px; border-radius:24px 24px 0 0;">
                <div style="background:rgba(0,0,0,0.5); min-height:320px; border-radius:24px 24px 0 0; display:flex; align-items:center; justify-content:center;">
                    <h2 style="color:#fff; font-size:5rem; font-weight:900; text-align:center; letter-spacing:2px;">
                        Pwesto Company<br>Collaboration
                    </h2>
                </div>
            </div>
            <div style="background:#fbeec1; display:flex; justify-content:center; align-items:center; gap:5rem; padding:3.5rem 0; border-radius:0 0 24px 24px;">
                <img src='{{ asset('images/produktiv.png') }}' alt='Produktiv' style='height:180px; border-radius:28px; background:#fff; padding:2rem;'>
                <img src='{{ asset('images/Nest.png') }}' alt='Nest' style='height:180px; border-radius:28px; background:#fff; padding:2rem;'>
                <img src='{{ asset('images/media.jpg') }}' alt='Mesh Media' style='height:180px; border-radius:28px; background:#fff; padding:2rem;'>
            </div>
        </div>

        <!-- Footer -->
        <div style="background:#111; color:#fff; padding:3rem 0 1.5rem 0; margin-top:0;">
            <div style="max-width:1200px; margin:auto; display:flex; flex-wrap:wrap; justify-content:space-between; gap:2rem;">
                <div>
                    <div style="font-weight:700; margin-bottom:1rem;">FEATURE</div>
                    <div>Team Management</div>
                    <div>Tasks Schedule</div>
                    <div>File Manager</div>
                </div>
                <div>
                    <div style="font-weight:700; margin-bottom:1rem;">RESOURCES</div>
                    <div>Blog</div>
                    <div>Support</div>
                    <div>Newsletter</div>
                </div>
                <div>
                    <div style="font-weight:700; margin-bottom:1rem;">COMMUNITY</div>
                    <div>Twitter</div>
                    <div>Instagram</div>
                    <div>Facebook</div>
                    <div>Youtube</div>
                </div>
                <div>
                    <div style="font-weight:700; margin-bottom:1rem;">SUPPORT</div>
                    <div>My Account</div>
                    <div>Help & Support</div>
                    <div>Contact Us</div>
                </div>
                <div>
                    <div style="font-weight:700; margin-bottom:1rem;">COMPANY</div>
                    <div>Privacy Policy</div>
                    <div>Terms of Service</div>
                    <div>Code of Conduct</div>
                </div>
            </div>
            <div style="text-align:left; margin-top:2rem; font-size:1.1rem; font-weight:600;">
                Pwesto
            </div>
        </div>
    </div>
</body>
</html>
