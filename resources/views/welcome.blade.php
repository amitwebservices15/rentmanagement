<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>RentEase — Smart Rent Management</title>
    <link rel="stylesheet" href="{{ asset('assets/vendors/css/vendor.bundle.base.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendors/mdi/css/materialdesignicons.min.css') }}">
    <link rel="shortcut icon" href="{{ asset('assets/images/favicon.png') }}">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Nunito', sans-serif;
            color: #2d3748;
            background: #fff;
        }

        /* ── Navbar ── */
        .navbar {
            position: fixed; top: 0; width: 100%; z-index: 999;
            background: #fff;
            box-shadow: 0 2px 12px rgba(0,0,0,.08);
            padding: 14px 0;
        }
        .navbar .container {
            max-width: 1100px; margin: 0 auto; padding: 0 24px;
            display: flex; align-items: center; justify-content: space-between;
        }
        .navbar-brand {
            font-size: 1.5rem; font-weight: 800;
            color: #4e73df; text-decoration: none; letter-spacing: -0.5px;
        }
        .navbar-brand span { color: #2d3748; }
        .nav-links { display: flex; gap: 12px; align-items: center; }
        .btn-login {
            padding: 8px 22px; border-radius: 6px; font-size: .875rem;
            font-weight: 600; text-decoration: none; border: 2px solid #4e73df;
            color: #4e73df; transition: all .2s;
        }
        .btn-login:hover { background: #4e73df; color: #fff; }
        .btn-signup {
            padding: 8px 22px; border-radius: 6px; font-size: .875rem;
            font-weight: 600; text-decoration: none;
            background: #4e73df; color: #fff; transition: all .2s;
        }
        .btn-signup:hover { background: #3a5fc8; color: #fff; }

        /* ── Hero ── */
        .hero {
            padding: 130px 24px 80px;
            background: linear-gradient(135deg, #f0f4ff 0%, #e8f0fe 100%);
            text-align: center;
        }
        .hero-badge {
            display: inline-block; background: #e8f0fe; color: #4e73df;
            font-size: .78rem; font-weight: 700; letter-spacing: 1px;
            text-transform: uppercase; padding: 6px 16px; border-radius: 20px;
            margin-bottom: 20px;
        }
        .hero h1 {
            font-size: 2.8rem; font-weight: 800; line-height: 1.2;
            color: #1a202c; margin-bottom: 18px;
        }
        .hero h1 span { color: #4e73df; }
        .hero p {
            font-size: 1.1rem; color: #718096; max-width: 560px;
            margin: 0 auto 36px; line-height: 1.7;
        }
        .hero-btns { display: flex; gap: 14px; justify-content: center; flex-wrap: wrap; }
        .btn-primary-lg {
            padding: 14px 36px; background: #4e73df; color: #fff;
            border-radius: 8px; font-size: 1rem; font-weight: 700;
            text-decoration: none; transition: all .2s;
            box-shadow: 0 4px 14px rgba(78,115,223,.35);
        }
        .btn-primary-lg:hover { background: #3a5fc8; color: #fff; transform: translateY(-1px); }
        .btn-outline-lg {
            padding: 14px 36px; border: 2px solid #4e73df; color: #4e73df;
            border-radius: 8px; font-size: 1rem; font-weight: 700;
            text-decoration: none; transition: all .2s;
        }
        .btn-outline-lg:hover { background: #4e73df; color: #fff; }

        /* ── Stats ── */
        .stats {
            background: #4e73df; padding: 40px 24px;
        }
        .stats .container {
            max-width: 1100px; margin: 0 auto;
            display: flex; justify-content: space-around;
            flex-wrap: wrap; gap: 20px; text-align: center;
        }
        .stat-item { color: #fff; }
        .stat-item h3 { font-size: 2rem; font-weight: 800; margin-bottom: 4px; }
        .stat-item p { font-size: .9rem; opacity: .85; }

        /* ── Features ── */
        .features { padding: 80px 24px; background: #fff; }
        .features .container { max-width: 1100px; margin: 0 auto; }
        .section-title { text-align: center; margin-bottom: 50px; }
        .section-title h2 { font-size: 2rem; font-weight: 800; color: #1a202c; margin-bottom: 10px; }
        .section-title p { color: #718096; font-size: 1rem; }

        .features-grid {
            display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 28px;
        }
        .feature-card {
            padding: 32px 28px; border-radius: 12px;
            border: 1px solid #e2e8f0; transition: all .25s;
        }
        .feature-card:hover {
            box-shadow: 0 8px 30px rgba(0,0,0,.1);
            transform: translateY(-4px); border-color: #4e73df;
        }
        .feature-icon {
            width: 52px; height: 52px; border-radius: 12px;
            background: #e8f0fe; display: flex; align-items: center;
            justify-content: center; margin-bottom: 18px;
        }
        .feature-icon i { font-size: 1.5rem; color: #4e73df; }
        .feature-card h4 { font-size: 1.05rem; font-weight: 700; margin-bottom: 10px; color: #1a202c; }
        .feature-card p { font-size: .9rem; color: #718096; line-height: 1.6; }

        /* ── How it works ── */
        .how { padding: 80px 24px; background: #f7fafc; }
        .how .container { max-width: 1100px; margin: 0 auto; }
        .steps {
            display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 32px; margin-top: 50px;
        }
        .step { text-align: center; }
        .step-num {
            width: 56px; height: 56px; border-radius: 50%;
            background: #4e73df; color: #fff; font-size: 1.3rem;
            font-weight: 800; display: flex; align-items: center;
            justify-content: center; margin: 0 auto 18px;
        }
        .step h4 { font-size: 1rem; font-weight: 700; margin-bottom: 8px; color: #1a202c; }
        .step p { font-size: .875rem; color: #718096; line-height: 1.6; }

        /* ── CTA ── */
        .cta {
            padding: 80px 24px;
            background: linear-gradient(135deg, #4e73df 0%, #3a5fc8 100%);
            text-align: center;
        }
        .cta h2 { font-size: 2rem; font-weight: 800; color: #fff; margin-bottom: 14px; }
        .cta p { color: rgba(255,255,255,.85); font-size: 1rem; margin-bottom: 32px; }
        .btn-white {
            padding: 14px 40px; background: #fff; color: #4e73df;
            border-radius: 8px; font-size: 1rem; font-weight: 700;
            text-decoration: none; transition: all .2s;
            box-shadow: 0 4px 14px rgba(0,0,0,.15);
        }
        .btn-white:hover { background: #f0f4ff; color: #3a5fc8; transform: translateY(-1px); }

        /* ── Footer ── */
        footer {
            background: #1a202c; color: #a0aec0;
            padding: 30px 24px; text-align: center; font-size: .875rem;
        }
        footer a { color: #4e73df; text-decoration: none; }

        /* ── Responsive ── */
        @media (max-width: 640px) {
            .hero h1 { font-size: 1.9rem; }
            .hero p { font-size: 1rem; }
            .stat-item h3 { font-size: 1.5rem; }
            .navbar-brand { font-size: 1.2rem; }
        }
    </style>
</head>
<body>

    {{-- Navbar --}}
    <nav class="navbar">
        <div class="container">
            <a href="{{ url('/') }}" class="navbar-brand">
                Rent<span>Ease</span>
            </a>
            <div class="nav-links">
                @auth
                    <a href="{{ url('/dashboard') }}" class="btn-signup">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="btn-login">Login</a>
                    <a href="{{ route('register') }}" class="btn-signup">Get Started</a>
                @endauth
            </div>
        </div>
    </nav>

    {{-- Hero --}}
    <section class="hero">
        <div class="hero-badge">🏠 Smart Rent Management</div>
        <h1>Manage Your Properties<br><span>Effortlessly</span></h1>
        <p>Track tenants, collect rent, manage rooms and get WhatsApp alerts — all in one simple platform built for property owners.</p>
        <div class="hero-btns">
            <a href="{{ route('register') }}" class="btn-primary-lg">Start Free Today</a>
            <a href="{{ route('login') }}" class="btn-outline-lg">Login</a>
        </div>
    </section>

    {{-- Stats --}}
    <section class="stats">
        <div class="container">
            <div class="stat-item">
                <h3>500+</h3>
                <p>Properties Managed</p>
            </div>
            <div class="stat-item">
                <h3>2,000+</h3>
                <p>Tenants Tracked</p>
            </div>
            <div class="stat-item">
                <h3>98%</h3>
                <p>On-time Rent Collection</p>
            </div>
            <div class="stat-item">
                <h3>24/7</h3>
                <p>WhatsApp Alerts</p>
            </div>
        </div>
    </section>

    {{-- Features --}}
    <section class="features">
        <div class="container">
            <div class="section-title">
                <h2>Everything You Need</h2>
                <p>A complete toolkit for property owners to manage rentals with ease</p>
            </div>
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon"><i class="mdi mdi-home-city"></i></div>
                    <h4>Property Management</h4>
                    <p>Add multiple properties — hostels, PGs, flats, or commercial spaces. Organize everything in one place.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon"><i class="mdi mdi-door"></i></div>
                    <h4>Room Tracking</h4>
                    <p>Manage individual rooms with rent, capacity, floor, and availability status in real time.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon"><i class="mdi mdi-account-group"></i></div>
                    <h4>Tenant Management</h4>
                    <p>Store tenant details, ID proofs, photos and assign them to rooms with move-in dates.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon"><i class="mdi mdi-currency-inr"></i></div>
                    <h4>Rent & Billing</h4>
                    <p>Generate monthly rent bills with electricity charges. Track paid, unpaid and partial payments.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon"><i class="mdi mdi-whatsapp"></i></div>
                    <h4>WhatsApp Alerts</h4>
                    <p>Send rent reminders and payment confirmations directly to tenants via WhatsApp.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon"><i class="mdi mdi-shield-check"></i></div>
                    <h4>Secure & Private</h4>
                    <p>Each owner sees only their own data. Role-based access keeps everything safe and isolated.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- How it works --}}
    <section class="how">
        <div class="container">
            <div class="section-title">
                <h2>How It Works</h2>
                <p>Get up and running in minutes</p>
            </div>
            <div class="steps">
                <div class="step">
                    <div class="step-num">1</div>
                    <h4>Create Account</h4>
                    <p>Register as a property owner with your name, email and mobile number.</p>
                </div>
                <div class="step">
                    <div class="step-num">2</div>
                    <h4>Add Property</h4>
                    <p>Add your property details — name, type, address and total rooms.</p>
                </div>
                <div class="step">
                    <div class="step-num">3</div>
                    <h4>Add Rooms</h4>
                    <p>Create rooms with rent amount, capacity and floor details.</p>
                </div>
                <div class="step">
                    <div class="step-num">4</div>
                    <h4>Assign Tenants</h4>
                    <p>Add tenants and assign them to rooms. Start tracking rent automatically.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- CTA --}}
    <section class="cta">
        <h2>Ready to Simplify Rent Management?</h2>
        <p>Join hundreds of property owners already using RentEase</p>
        <a href="{{ route('register') }}" class="btn-white">Create Free Account</a>
    </section>

    {{-- Footer --}}
    <footer>
        <p>&copy; {{ date('Y') }} RentEase. All rights reserved. &nbsp;|&nbsp;
            <a href="{{ route('login') }}">Login</a> &nbsp;|&nbsp;
            <a href="{{ route('register') }}">Register</a>
        </p>
    </footer>

</body>
</html>
