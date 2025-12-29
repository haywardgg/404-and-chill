<?php
/* ===========================
   STATUS & HEADERS
=========================== */

$code = http_response_code();
if ($code < 400) {
    $code = 404;
}
http_response_code($code);

// Bot-friendly headers
header('Content-Type: text/html; charset=utf-8');
header('X-Robots-Tag: noindex, nofollow, noarchive');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
header('Expires: 0');

// Rate-limit hint (harmless to humans)
header('Retry-After: 30');
header('X-RateLimit-Hint: Too many invalid requests');

/* ===========================
   REQUEST INFO
=========================== */

$path = htmlspecialchars($_SERVER['REQUEST_URI'] ?? '/', ENT_QUOTES);
$ua   = $_SERVER['HTTP_USER_AGENT'] ?? '';

// Conservative bot detection
$isBot = (bool) preg_match('/bot|crawl|slurp|spider|bing|google|yandex|duckduck/i', $ua);

/* ===========================
   GEO (Cloudflare-aware)
=========================== */

$country = $_SERVER['HTTP_CF_IPCOUNTRY'] ?? null;

$geoJokes = [
    'US' => "Even freedom couldn’t find this page.",
    'GB' => "We searched politely. Still missing.",
    'DE' => "Efficiency confirmed: page not found.",
    'FR' => "This page has gone on strike.",
    'IT' => "Beautiful idea. Wrong location.",
    'ES' => "This page is taking a siesta.",
    'JP' => "This page left quietly without notice.",
    'AU' => "Checked upside down. Still gone.",
];

$geoLine = $geoJokes[$country] ?? "This page is missing internationally.";

/* ===========================
   HUMOUR POOL
=========================== */

$humour = [
    "This page went out for milk and joined a startup.",
    "404: The content you seek has ascended.",
    "This URL exists only in theory.",
    "Our servers checked everywhere. Even under the couch.",
    "This page is experiencing existential dread.",
    "Here lies a webpage that tried its best.",
    "The link was a lie.",
];

$message = $humour[array_rand($humour)];

/* ===========================
   ERROR TITLES
=========================== */

$titles = [
    404 => "Page Not Found",
    403 => "Access Denied",
    410 => "Gone Forever",
    500 => "Server Error",
];

$title = $titles[$code] ?? "Unexpected Error";

/* ===========================
   INLINE CSS FOR BOTS ONLY
=========================== */

$inlineCss = <<<CSS
:root {
    --bg:#0b1020;--card:rgba(255,255,255,.06);
    --border:rgba(255,255,255,.12);
    --text:#e5e7eb;--muted:#9ca3af;
}
*{box-sizing:border-box}
body{
    margin:0;min-height:100vh;
    font-family:system-ui,sans-serif;
    background:#0b1020;color:var(--text);
    display:flex;align-items:center;justify-content:center
}
.card{
    max-width:560px;width:90%;
    padding:2.5rem;background:var(--card);
    border:1px solid var(--border);
    border-radius:18px;text-align:center
}
.code{font-size:4rem;font-weight:800}
.subtitle{color:var(--muted)}
.path{
    margin:1.5rem 0;padding:.7rem;
    font-family:monospace;
    background:#000;border-radius:8px
}
.footer{font-size:.75rem;color:var(--muted)}
CSS;
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<title><?= $code ?> — <?= htmlspecialchars($title) ?></title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<?php if ($isBot): ?>
<style><?= $inlineCss ?></style>
<?php else: ?>
<link rel="stylesheet" href="/404.css">
<?php endif; ?>

</head>
<body>

<div class="card">
    <div class="code"><?= $code ?></div>
    <div class="subtitle"><?= htmlspecialchars($title) ?></div>

    <p><?= htmlspecialchars($message) ?></p>
    <p style="color:#94a3b8"><?= htmlspecialchars($geoLine) ?></p>

    <div class="path"><?= $path ?></div>

    <?php if (!$isBot): ?>
    <div class="actions">
        <a class="button" href="/">Home</a>
        <a class="button secondary" href="javascript:history.back()">Go back</a>
    </div>
    <?php endif; ?>

    <div class="footer">
        <?= $isBot
            ? 'Crawler notice: excessive invalid URLs may be rate limited.'
            : 'If this keeps happening, it’s probably DNS.' ?>
    </div>
</div>

</body>
</html>
