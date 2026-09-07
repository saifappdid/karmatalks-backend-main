<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ekagrata KarmaTalks – Your Cosmic Journey Awaits</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link
        href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;1,300;1,400&family=Cinzel:wght@400;600&family=Jost:wght@300;400;500&display=swap"
        rel="stylesheet">
    <style>
        :root {
            --gold: #C9A84C;
            --gold-light: #E8C97A;
            --gold-dim: rgba(201, 168, 76, 0.15);
            --deep: #0A0710;
            --ink: #130E1F;
            --mist: #1E1630;
            --text: #EDE8DB;
            --text-muted: rgba(237, 232, 219, 0.55);
            --accent: #7B5EA7;
            --accent2: #4A2D6F;
        }

        *,
        *::before,
        *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            background: var(--deep);
            color: var(--text);
            font-family: 'Jost', sans-serif;
            font-weight: 300;
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* ── Starfield ── */
        .stars {
            position: fixed;
            inset: 0;
            pointer-events: none;
            z-index: 0;
            background:
                radial-gradient(ellipse at 20% 30%, rgba(123, 94, 167, 0.18) 0%, transparent 60%),
                radial-gradient(ellipse at 80% 70%, rgba(74, 45, 111, 0.22) 0%, transparent 55%),
                radial-gradient(ellipse at 50% 10%, rgba(201, 168, 76, 0.07) 0%, transparent 40%);
        }

        .star-layer {
            position: fixed;
            inset: 0;
            pointer-events: none;
            background-image:
                radial-gradient(1px 1px at 10% 15%, rgba(255, 255, 255, 0.8) 0%, transparent 100%),
                radial-gradient(1px 1px at 25% 45%, rgba(255, 255, 255, 0.6) 0%, transparent 100%),
                radial-gradient(1.5px 1.5px at 40% 20%, rgba(255, 255, 255, 0.9) 0%, transparent 100%),
                radial-gradient(1px 1px at 55% 60%, rgba(255, 255, 255, 0.5) 0%, transparent 100%),
                radial-gradient(1px 1px at 70% 35%, rgba(255, 255, 255, 0.7) 0%, transparent 100%),
                radial-gradient(1.5px 1.5px at 85% 75%, rgba(255, 255, 255, 0.8) 0%, transparent 100%),
                radial-gradient(1px 1px at 15% 80%, rgba(255, 255, 255, 0.6) 0%, transparent 100%),
                radial-gradient(1px 1px at 92% 25%, rgba(255, 255, 255, 0.7) 0%, transparent 100%),
                radial-gradient(1px 1px at 48% 90%, rgba(255, 255, 255, 0.5) 0%, transparent 100%),
                radial-gradient(1.5px 1.5px at 63% 8%, rgba(201, 168, 76, 0.9) 0%, transparent 100%),
                radial-gradient(1px 1px at 33% 68%, rgba(255, 255, 255, 0.6) 0%, transparent 100%),
                radial-gradient(1px 1px at 78% 55%, rgba(255, 255, 255, 0.4) 0%, transparent 100%),
                radial-gradient(1.5px 1.5px at 5% 50%, rgba(201, 168, 76, 0.7) 0%, transparent 100%),
                radial-gradient(1px 1px at 90% 10%, rgba(255, 255, 255, 0.8) 0%, transparent 100%),
                radial-gradient(1px 1px at 58% 40%, rgba(255, 255, 255, 0.5) 0%, transparent 100%);
        }

        /* ── Layout ── */
        .page {
            position: relative;
            z-index: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 0 20px;
        }

        /* ── Hero ── */
        .hero {
            width: 100%;
            max-width: 680px;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            padding: 64px 24px 40px;
            animation: fadeUp 1s ease both;
        }

        .logo-ring {
            width: 96px;
            height: 96px;
            border-radius: 50%;
            border: 1.5px solid var(--gold);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 28px;
            box-shadow: 0 0 32px rgba(201, 168, 76, 0.25), inset 0 0 20px rgba(201, 168, 76, 0.06);
            animation: pulse-ring 4s ease-in-out infinite;
            overflow: hidden;
        }

        .logo-ring img {
            width: 76px;
            height: 76px;
            object-fit: contain;
            border-radius: 50%;
        }

        @keyframes pulse-ring {

            0%,
            100% {
                box-shadow: 0 0 32px rgba(201, 168, 76, 0.25), inset 0 0 20px rgba(201, 168, 76, 0.06);
            }

            50% {
                box-shadow: 0 0 52px rgba(201, 168, 76, 0.4), inset 0 0 28px rgba(201, 168, 76, 0.1);
            }
        }

        .eyebrow {
            font-family: 'Cinzel', serif;
            font-size: 10px;
            letter-spacing: 0.3em;
            text-transform: uppercase;
            color: var(--gold);
            margin-bottom: 14px;
            animation: fadeUp 1s 0.15s ease both;
        }

        h1 {
            font-family: 'Cinzel', serif;
            font-size: clamp(28px, 8vw, 46px);
            font-weight: 600;
            line-height: 1.15;
            letter-spacing: 0.02em;
            color: var(--text);
            margin-bottom: 6px;
            animation: fadeUp 1s 0.2s ease both;
        }

        h1 span {
            color: var(--gold);
            font-style: italic;
            font-family: 'Cormorant Garamond', serif;
        }

        .tagline {
            font-family: 'Cormorant Garamond', serif;
            font-size: clamp(17px, 4vw, 22px);
            font-style: italic;
            color: var(--text-muted);
            margin-bottom: 28px;
            animation: fadeUp 1s 0.3s ease both;
        }

        .divider {
            width: 80px;
            height: 1px;
            background: linear-gradient(90deg, transparent, var(--gold), transparent);
            margin-bottom: 24px;
            animation: fadeUp 1s 0.35s ease both;
        }

        .sub {
            font-size: 14px;
            line-height: 1.8;
            color: var(--text-muted);
            max-width: 460px;
            margin-bottom: 44px;
            animation: fadeUp 1s 0.4s ease both;
        }

        /* ── CTA Buttons ── */
        .cta-group {
            display: flex;
            gap: 14px;
            flex-wrap: wrap;
            justify-content: center;
            animation: fadeUp 1s 0.5s ease both;
            margin-bottom: 56px;
        }

        .btn-store {
            display: flex;
            align-items: center;
            gap: 12px;
            background: rgba(201, 168, 76, 0.08);
            border: 1px solid rgba(201, 168, 76, 0.4);
            color: var(--text);
            padding: 14px 22px;
            border-radius: 10px;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.3s ease;
            min-width: 180px;
            backdrop-filter: blur(6px);
        }

        .btn-store:hover {
            background: rgba(201, 168, 76, 0.18);
            border-color: var(--gold);
            transform: translateY(-2px);
            box-shadow: 0 8px 28px rgba(201, 168, 76, 0.18);
        }

        .btn-store svg {
            width: 26px;
            height: 26px;
            flex-shrink: 0;
        }

        .btn-store .btn-text {
            display: flex;
            flex-direction: column;
            text-align: left;
        }

        .btn-store .btn-sub {
            font-size: 10px;
            color: var(--text-muted);
            letter-spacing: 0.05em;
        }

        .btn-store .btn-name {
            font-family: 'Cinzel', serif;
            font-size: 13px;
            letter-spacing: 0.04em;
        }

        /* ── Services grid ── */
        .section {
            width: 100%;
            max-width: 680px;
            margin-bottom: 56px;
        }

        .section-title {
            font-family: 'Cinzel', serif;
            font-size: 11px;
            letter-spacing: 0.25em;
            color: var(--gold);
            text-transform: uppercase;
            text-align: center;
            margin-bottom: 28px;
        }

        .services {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 12px;
        }

        @media(max-width: 440px) {
            .services {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        .service-pill {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(201, 168, 76, 0.12);
            border-radius: 10px;
            padding: 14px 10px;
            text-align: center;
            transition: all 0.3s;
            animation: fadeUp 1s ease both;
        }

        .service-pill:hover {
            background: var(--gold-dim);
            border-color: rgba(201, 168, 76, 0.35);
        }

        .service-pill .icon {
            font-size: 22px;
            display: block;
            margin-bottom: 6px;
        }

        .service-pill .name {
            font-size: 11px;
            color: var(--text-muted);
            line-height: 1.4;
        }

        /* ── Stats ── */
        .stats {
            display: flex;
            gap: 0;
            border: 1px solid rgba(201, 168, 76, 0.15);
            border-radius: 14px;
            overflow: hidden;
            margin-bottom: 56px;
            width: 100%;
            max-width: 680px;
            backdrop-filter: blur(4px);
            animation: fadeUp 1s 0.6s ease both;
        }

        .stat {
            flex: 1;
            padding: 22px 12px;
            text-align: center;
            border-right: 1px solid rgba(201, 168, 76, 0.12);
        }

        .stat:last-child {
            border-right: none;
        }

        .stat-num {
            font-family: 'Cormorant Garamond', serif;
            font-size: 28px;
            color: var(--gold);
            font-weight: 600;
        }

        .stat-label {
            font-size: 10px;
            color: var(--text-muted);
            letter-spacing: 0.1em;
            text-transform: uppercase;
            margin-top: 2px;
        }

        /* ── Testimonial ── */
        .testimonial {
            width: 100%;
            max-width: 540px;
            background: rgba(255, 255, 255, 0.02);
            border: 1px solid rgba(201, 168, 76, 0.15);
            border-radius: 16px;
            padding: 28px 28px;
            text-align: center;
            margin-bottom: 56px;
            animation: fadeUp 1s 0.7s ease both;
            position: relative;
        }

        .testimonial::before {
            content: '\201C';
            font-family: 'Cormorant Garamond', serif;
            font-size: 72px;
            color: rgba(201, 168, 76, 0.2);
            position: absolute;
            top: 4px;
            left: 18px;
            line-height: 1;
        }

        .testimonial p {
            font-family: 'Cormorant Garamond', serif;
            font-size: 16px;
            font-style: italic;
            color: var(--text-muted);
            line-height: 1.7;
            margin-bottom: 14px;
        }

        .testimonial cite {
            font-size: 11px;
            color: var(--gold);
            letter-spacing: 0.1em;
        }

        /* ── Final CTA ── */
        .final-cta {
            width: 100%;
            max-width: 680px;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 40px 24px 56px;
            text-align: center;
            animation: fadeUp 1s 0.8s ease both;
        }

        .final-cta h2 {
            font-family: 'Cormorant Garamond', serif;
            font-size: clamp(22px, 6vw, 32px);
            font-style: italic;
            margin-bottom: 10px;
        }

        .final-cta p {
            font-size: 13px;
            color: var(--text-muted);
            margin-bottom: 28px;
        }

        .btn-primary {
            display: inline-block;
            background: linear-gradient(135deg, var(--gold), #A07830);
            color: var(--deep);
            font-family: 'Cinzel', serif;
            font-size: 13px;
            letter-spacing: 0.12em;
            padding: 16px 38px;
            border-radius: 50px;
            text-decoration: none;
            cursor: pointer;
            border: none;
            transition: all 0.3s;
            box-shadow: 0 4px 24px rgba(201, 168, 76, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 36px rgba(201, 168, 76, 0.45);
        }

        /* ── Footer ── */
        footer {
            border-top: 1px solid rgba(201, 168, 76, 0.1);
            width: 100%;
            padding: 24px;
            text-align: center;
            font-size: 11px;
            color: var(--text-muted);
            letter-spacing: 0.05em;
            position: relative;
            z-index: 1;
        }

        /* ── Animations ── */
        @keyframes fadeUp {
            from {
                opacity: 0;
                transform: translateY(22px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* stagger services */
        .service-pill:nth-child(1) {
            animation-delay: 0.55s;
        }

        .service-pill:nth-child(2) {
            animation-delay: 0.6s;
        }

        .service-pill:nth-child(3) {
            animation-delay: 0.65s;
        }

        .service-pill:nth-child(4) {
            animation-delay: 0.7s;
        }

        .service-pill:nth-child(5) {
            animation-delay: 0.75s;
        }

        .service-pill:nth-child(6) {
            animation-delay: 0.8s;
        }

        .service-pill:nth-child(7) {
            animation-delay: 0.85s;
        }

        .service-pill:nth-child(8) {
            animation-delay: 0.9s;
        }

        .service-pill:nth-child(9) {
            animation-delay: 0.95s;
        }

        /* Ornament */
        .ornament {
            color: var(--gold);
            opacity: 0.5;
            font-size: 18px;
            margin: 0 8px;
        }
    </style>
</head>

<body>

    <div class="stars"></div>
    <div class="star-layer"></div>

    <div class="page">

        <!-- ── HERO ── -->
        <section class="hero">
            <div class="logo-ring">
                <img src="https://ekagratakarmatalks.com/wp-content/uploads/2024/01/Logo-3.png"
                    alt="Ekagrata KarmaTalks Logo"
                    onerror="this.style.display='none'; this.parentElement.innerHTML='✦'">
            </div>

            <p class="eyebrow">✦ &nbsp; Ekagrata KarmaTalks &nbsp; ✦</p>
            <h1>Your Cosmic Journey<br>Begins <span>Within</span></h1>
            <p class="tagline">Clarity · Healing · Personal Growth</p>
            <div class="divider"></div>
            <p class="sub">Connect with India's finest spiritual consultants — Akashic Records, Tarot, Numerology,
                Reiki, Past Life Regression &amp; more. Book sessions instantly on the app.</p>

            <div class="cta-group">
                <!-- Android -->
                <a class="btn-store" id="playstore-btn" href="#" onclick="return openStore('android')">
                    <svg viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M3.18 23.76a2 2 0 0 1-.98-.26 2.06 2.06 0 0 1-1.02-1.8V2.3a2.06 2.06 0 0 1 1.02-1.8 2 2 0 0 1 2.04.04l16.67 9.7a2.06 2.06 0 0 1 0 3.52L4.24 23.46a2 2 0 0 1-1.06.3Z"
                            fill="var(--gold)" />
                    </svg>
                    <div class="btn-text">
                        <span class="btn-sub">Get it on</span>
                        <span class="btn-name">Google Play</span>
                    </div>
                </a>

                <!-- iOS -->
                <a class="btn-store" id="appstore-btn" href="#" onclick="return openStore('ios')">
                    <svg viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M18.71 19.5c-.83 1.24-1.71 2.45-3.05 2.47-1.34.03-1.77-.79-3.29-.79-1.53 0-2 .77-3.27.82-1.31.05-2.3-1.32-3.14-2.53C4.25 17 2.94 12.45 4.7 9.39c.87-1.52 2.43-2.48 4.12-2.51 1.28-.02 2.5.87 3.29.87.78 0 2.26-1.07 3.8-.91.65.03 2.47.26 3.64 1.98-.09.06-2.17 1.28-2.15 3.81.03 3.02 2.65 4.03 2.68 4.04-.03.07-.42 1.44-1.38 2.83M13 3.5c.73-.83 1.94-1.46 2.94-1.5.13 1.17-.34 2.35-1.04 3.19-.69.85-1.83 1.51-2.95 1.42-.15-1.15.41-2.35 1.05-3.11Z"
                            fill="var(--gold)" />
                    </svg>
                    <div class="btn-text">
                        <span class="btn-sub">Download on the</span>
                        <span class="btn-name">App Store</span>
                    </div>
                </a>
            </div>
        </section>

        <!-- ── STATS ── -->
        <div class="stats">
            <div class="stat">
                <div class="stat-num">20+</div>
                <div class="stat-label">Experts</div>
            </div>
            <div class="stat">
                <div class="stat-num">500+</div>
                <div class="stat-label">Happy Clients</div>
            </div>
            <div class="stat">
                <div class="stat-label" style="font-size:18px; color:var(--gold); padding-top:4px;">★★★★★</div>
                <div class="stat-label" style="margin-top:4px;">Top Rated</div>
            </div>
        </div>

        <!-- ── SERVICES ── -->
        <section class="section">
            <p class="section-title"><span class="ornament">✦</span> Our Services <span class="ornament">✦</span></p>
            <div class="services">
                <div class="service-pill"><span class="icon">🔮</span><span class="name">Akashic Records
                        Reading</span></div>
                <div class="service-pill"><span class="icon">🃏</span><span class="name">Tarot Card Reading</span>
                </div>
                <div class="service-pill"><span class="icon">🔢</span><span class="name">Numerology</span></div>
                <div class="service-pill"><span class="icon">✋</span><span class="name">Palm Reading</span></div>
                <div class="service-pill"><span class="icon">🌿</span><span class="name">Reiki Healing</span></div>
                <div class="service-pill"><span class="icon">⏳</span><span class="name">Past Life Regression</span>
                </div>
                <div class="service-pill"><span class="icon">🐾</span><span class="name">Animal Communicator</span>
                </div>
                <div class="service-pill"><span class="icon">🏠</span><span class="name">Vastu Consultation</span>
                </div>
                <div class="service-pill"><span class="icon">🌸</span><span class="name">Face Reading</span></div>
            </div>
        </section>

        <!-- ── TESTIMONIAL ── -->
        <div class="testimonial">
            <p>"The session was so beautiful and impacting. I am feeling so much serene and calm. Maam was so insightful
                — she truly is a gift to the world."</p>
            <cite>— Abhirami Prasad &nbsp;·&nbsp; Past Life Regression</cite>
        </div>

        <!-- ── FINAL CTA ── -->
        <div class="final-cta">
            <h2>Your Path to Clarity is One Tap Away</h2>
            <p>Download the app and book your first consultation today</p>
            <a class="btn-primary" href="#" onclick="return openStore('auto')">Download the App</a>
        </div>

    </div>

    <footer>
        © 2025 Ekagrata One Universal Pvt. Ltd. &nbsp;·&nbsp; Gandhidham, Gujarat &nbsp;·&nbsp;
        ekagratakarmatalks@gmail.com
    </footer>

    <script>
        // ── Deep Link Logic ──
        // Replace these with your actual store URLs once published
        const PLAY_STORE_URL = "https://play.google.com/store/apps/details?id=com.karmatalks.user";
        const APP_STORE_URL = "https://apps.apple.com/us/app/karmatalks/id6753060251";
        const FALLBACK_URL = "https://ekagratakarmatalks.com";

        // Detect platform
        function detectPlatform() {
            const ua = navigator.userAgent || navigator.vendor || window.opera;
            if (/android/i.test(ua)) return 'android';
            if (/iPad|iPhone|iPod/.test(ua) && !window.MSStream) return 'ios';
            return 'desktop';
        }

        function openStore(target) {
            const platform = target === 'auto' ? detectPlatform() : target;

            if (platform === 'android') {
                window.location.href = PLAY_STORE_URL;
            } else if (platform === 'ios') {
                window.location.href = APP_STORE_URL;
            } else {
                // Desktop: show both store buttons highlighted, scroll up
                document.querySelector('.cta-group').scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });
                document.querySelectorAll('.btn-store').forEach(btn => {
                    btn.style.borderColor = 'var(--gold)';
                    btn.style.background = 'rgba(201,168,76,0.18)';
                });
            }
            return false; // prevent default href="#"
        }

        // Auto-redirect on page load if on mobile
        (function autoRedirect() {
            const platform = detectPlatform();
            // Only auto-redirect if user came via a share/referral link (has ?ref= param)
            const params = new URLSearchParams(window.location.search);
            if (params.has('ref') || params.has('referral')) {
                if (platform === 'android') {
                    setTimeout(() => {
                        window.location.href = PLAY_STORE_URL;
                    }, 1500);
                } else if (platform === 'ios') {
                    setTimeout(() => {
                        window.location.href = APP_STORE_URL;
                    }, 1500);
                }
            }
        })();


        let referralLink = window.location.href;
        let device = "{{ $device }}";

        navigator.clipboard.writeText(referralLink).catch(() => {
            console.log("Clipboard copy blocked by browser");
        });

        setTimeout(() => {

            if (device === "android") {
                window.location.href =
                    "https://play.google.com/store/apps/details?id=com.karmatalks.user";
            } else if (device === "iphone") {
                window.location.href =
                    "https://apps.apple.com/us/app/karmatalks/id6753060251";
            } else {
                window.location.href =
                    "https://play.google.com/store/apps/details?id=com.karmatalks.user";
            }

        }, 800);
    </script>

</body>

</html>
