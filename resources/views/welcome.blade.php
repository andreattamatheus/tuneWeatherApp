<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="A Laravel weather API for current conditions and five-day forecasts.">
    <title>Weather API · Laravel</title>
    <style>
        :root { --ink:#17232b; --muted:#63717a; --cream:#f6f2ee; --peach:#ffb393; --coral:#f5654d; --navy:#14354a; --line:#dce4e6; }
        * { box-sizing:border-box; } html { scroll-behavior:smooth; } body { margin:0; color:var(--ink); background:#fff; font:16px/1.6 ui-sans-serif,system-ui,-apple-system,"Segoe UI",sans-serif; }
        a { color:inherit; } .skip-link { position:absolute; left:-999px; top:1rem; padding:.65rem 1rem; background:var(--ink); color:#fff; border-radius:999px; } .skip-link:focus { left:1rem; z-index:2; }
        .container { width:min(1120px,calc(100% - 40px)); margin:auto; } header { padding:24px 0; display:flex; justify-content:space-between; align-items:center; gap:24px; } .brand { display:flex; align-items:center; gap:12px; text-decoration:none; font-weight:800; letter-spacing:-.03em; } .brand-mark { display:grid; place-items:center; width:42px; height:42px; border-radius:14px; color:#fff; background:linear-gradient(145deg,var(--coral),var(--peach)); box-shadow:0 10px 24px #f5654d40; } nav { display:flex; gap:20px; color:var(--muted); font-size:.92rem; } nav a { text-decoration:none; } nav a:hover { color:var(--coral); }
        .hero { padding:72px 0 88px; background:linear-gradient(145deg,#fff 10%,var(--cream) 100%); } .hero-grid { display:grid; grid-template-columns:1.12fr .88fr; gap:64px; align-items:center; } .eyebrow { color:var(--coral); font-size:.78rem; font-weight:800; letter-spacing:.14em; text-transform:uppercase; } h1,h2 { margin:0; line-height:1.08; letter-spacing:-.055em; } h1 { max-width:680px; margin-top:14px; font-size:clamp(2.8rem,7vw,5.7rem); } .lede { max-width:560px; margin:24px 0 32px; color:var(--muted); font-size:1.15rem; } .actions { display:flex; flex-wrap:wrap; gap:12px; } .button { display:inline-flex; align-items:center; justify-content:center; min-height:48px; padding:0 22px; border-radius:999px; font-weight:750; text-decoration:none; } .button-primary { color:#fff; background:var(--coral); box-shadow:0 12px 25px #f5654d40; } .button-secondary { border:1px solid var(--ink); }
        .weather-card { position:relative; overflow:hidden; padding:28px; border-radius:28px; color:#fff; background:linear-gradient(155deg,var(--navy),#28617a); box-shadow:0 24px 55px #14354a30; } .weather-card:after { content:""; position:absolute; width:190px; height:190px; right:-70px; top:-70px; border-radius:50%; background:var(--peach); opacity:.75; } .weather-icon { position:relative; z-index:1; font-size:4rem; } .weather-card h2 { position:relative; z-index:1; margin-top:28px; font-size:2rem; } .weather-card p { position:relative; z-index:1; margin:.35rem 0 0; color:#d9edf1; } .metric { position:relative; z-index:1; display:flex; gap:22px; margin-top:28px; padding-top:20px; border-top:1px solid #ffffff38; } .metric strong { display:block; font-size:1.35rem; } .metric span { color:#c8dce1; font-size:.82rem; }
        section { padding:88px 0; } .section-intro { max-width:590px; margin-bottom:34px; } h2 { font-size:clamp(2rem,4vw,3.2rem); } .section-intro p { color:var(--muted); } .cards { display:grid; grid-template-columns:repeat(3,1fr); gap:16px; } .card { padding:25px; border:1px solid var(--line); border-radius:20px; background:#fff; } .card:hover { border-color:var(--peach); box-shadow:0 14px 35px #17232b12; } .card-number { color:var(--coral); font-weight:800; } .card h3 { margin:18px 0 8px; font-size:1.1rem; } .card p { margin:0; color:var(--muted); font-size:.94rem; } .code-panel { display:grid; grid-template-columns:1fr 1fr; gap:28px; align-items:stretch; padding:32px; border-radius:24px; background:var(--cream); } .code-panel p { color:var(--muted); } pre { overflow:auto; margin:0; padding:22px; border-radius:16px; color:#e9f5f5; background:var(--navy); font: .84rem/1.7 ui-monospace,SFMono-Regular,Menlo,monospace; } footer { padding:30px 0; border-top:1px solid var(--line); color:var(--muted); font-size:.9rem; }
        :focus-visible { outline:3px solid var(--coral); outline-offset:4px; } @media (max-width:760px) { .container { width:min(100% - 28px,560px); } header { align-items:flex-start; } nav { gap:12px; } .hero { padding:48px 0 60px; } .hero-grid,.code-panel { grid-template-columns:1fr; gap:36px; } .cards { grid-template-columns:1fr; } section { padding:60px 0; } h1 { font-size:clamp(2.7rem,15vw,4.6rem); } }
    </style>
</head>
<body>
<a class="skip-link" href="#main-content">Skip to content</a>
<header class="container" aria-label="Primary navigation">
    <a class="brand" href="{{ url('/') }}"><span class="brand-mark" aria-hidden="true">☼</span><span>Weather API</span></a>
    <nav><a href="#capabilities">Capabilities</a><a href="#quick-start">Quick start</a></nav>
</header>
<main id="main-content">
    <section class="hero"><div class="container hero-grid">
        <div><div class="eyebrow">Laravel · OpenWeatherMap</div><h1>Forecasts with a clearer point of view.</h1><p class="lede">Search any city, save favourite locations, and retrieve current conditions plus a five-day forecast through a focused, authenticated API.</p><div class="actions"><a class="button button-primary" href="#quick-start">Explore the API <span aria-hidden="true">&nbsp;→</span></a><a class="button button-secondary" href="#capabilities">See what it supports</a></div></div>
        <aside class="weather-card" aria-label="Example forecast card"><div class="weather-icon" aria-hidden="true">☀︎</div><h2>Built for daily decisions.</h2><p>A small API surface for the places your users care about.</p><div class="metric"><div><strong>5 days</strong><span>forecast range</span></div><div><strong>Global</strong><span>city search</span></div></div></aside>
    </div></section>
    <section id="capabilities"><div class="container"><div class="section-intro"><div class="eyebrow">One useful seam</div><h2>Everything needed to make weather feel personal.</h2><p>The API keeps authentication, saved places, and forecast retrieval separate so a frontend can stay simple and responsive.</p></div><div class="cards">
        <article class="card"><span class="card-number">01</span><h3>Live forecasts</h3><p>Retrieve the most recent forecast for a city and state with a consistent resource response.</p></article>
        <article class="card"><span class="card-number">02</span><h3>Saved locations</h3><p>Store a user’s locations asynchronously and let the cleanup job remove stale data.</p></article>
        <article class="card"><span class="card-number">03</span><h3>Ready for Vue</h3><p>Sanctum authentication and predictable versioned routes make the API easy to consume.</p></article>
    </div></div></section>
    <section id="quick-start"><div class="container code-panel"><div><div class="eyebrow">Quick start</div><h2>Make your first request.</h2><p>Authenticate first, then use the versioned forecast route from your frontend or API client.</p><a class="button button-primary" href="#quick-start">View request example</a></div><pre aria-label="Example API request"><code>GET /api/v1/get-location-forecast
Authorization: Bearer &lt;token&gt;

?city=Lisbon&amp;state=Lisbon</code></pre></div></section>
</main>
<footer><div class="container">Weather API · Laravel {{ Illuminate\Foundation\Application::VERSION }} · Sanctum-authenticated endpoints</div></footer>
</body>
</html>
