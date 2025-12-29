# 404-and-chill
When your page doesn’t exist, but at least the error page looks good. A stylish global Nginx error page with dark UI, random humour, and crawler-safe headers.

## 🚀 Quick drop-in for Nginx + PHP-FPM

This repo ships a ready-to-use PHP error handler (`var/www/errors/404.php`) and matching CSS (`var/www/errors/404.css`). Drop them on your server and tell Nginx to serve them for common error codes. The snippet below assumes:

- PHP-FPM socket at `unix:/run/php/php-fpm.sock`
- Error assets live in `/var/www/errors`

### 1) Add an include file (e.g., `/etc/nginx/inc/error-pages.conf`)

```nginx
error_page 404 403 410 500 /404.php;

location = /404.php {
    root /var/www/errors;
    internal;
    fastcgi_pass unix:/run/php/php-fpm.sock;
    include fastcgi_params;
    fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
}

location = /404.css {
    root /var/www/errors;
}

limit_req_zone $binary_remote_addr zone=404limit:10m rate=5r/m;
```

### 2) Reference the include in `nginx.conf`

Inside your `http { ... }` block (or a specific `server` block), pull in the snippet:

```nginx
include /etc/nginx/inc/error-pages.conf;
```

Reload Nginx when you are done:

```bash
sudo nginx -t && sudo systemctl reload nginx
```

## 🧠 What the PHP script does (beginner-friendly)

- 🌈 **Sets a sensible status code:** If the server didn’t send one, it defaults to **404** so browsers and bots know it’s an error.
- 🛡️ **Sends safe headers:** It tells search engines not to index the page, and disables caching so you always see fresh content.
- 🔍 **Bot-aware styling:** Crawlers get a tiny inline CSS block; humans get the full `404.css` for the dark, sleek UI.
- 🌍 **Geo-aware jokes:** If Cloudflare passes a country code, the page picks a location-based one-liner; otherwise it shows a generic message.
- 🎲 **Random humour:** Each visit picks a new friendly quip to lighten the error page.
- 🧭 **Shows the missing path:** The requested URL is echoed back safely (HTML-escaped) so you can spot typos.
- 🚦 **Rate-limit hint:** Adds headers like `Retry-After` as a gentle nudge for noisy bots without affecting real visitors.

With these bits combined, you get an error page that looks great, protects your SEO, and is friendly to both humans and crawlers. 🎉
