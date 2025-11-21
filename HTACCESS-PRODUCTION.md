##############################################
## Production .htaccess für Astro.js + IONOS
## Domain: wohlfühlgesundheit.de (IDN)
##############################################

##############################################
## Security Headers
##############################################

<IfModule mod_headers.c>
    ## Verhindert Clickjacking-Angriffe
    Header always set X-Frame-Options "SAMEORIGIN"
    Header always set X-Content-Type-Options "nosniff"
    Header always set X-XSS-Protection "1; mode=block"
    Header always set Referrer-Policy "strict-origin-when-cross-origin"

    ## Permissions Policy (ehemals Feature Policy)
    Header always set Permissions-Policy "geolocation=(), microphone=(), camera=()"

    ## Content Security Policy (für Astro.js optimiert)
    ## HINWEIS: 'unsafe-inline' ist für Astro Islands + View Transitions nötig
    ## 'unsafe-eval' wurde entfernt - Astro 5 braucht das nicht
    Header always set Content-Security-Policy "default-src 'self' https: data: 'unsafe-inline'; connect-src 'self' https://wohlfühlgesundheit.de https://www.wohlfühlgesundheit.de https://xn--wohlfhlgesundheit-62b.de https://www.xn--wohlfhlgesundheit-62b.de; img-src 'self' https: data:; style-src 'self' https: 'unsafe-inline'; script-src 'self' https: 'unsafe-inline'; font-src 'self' https: data:; frame-src 'self' https://zoom.us;"
</IfModule>

##############################################
## Performance: Browser Caching (Astro-optimiert)
##############################################

<IfModule mod_expires.c>
    ExpiresActive On

    ## Astro Build Assets (/_astro/*) - Immutable Caching
    ## Diese Dateien haben Content-Hashes im Dateinamen und ändern sich nie
    <FilesMatch "^/_astro/.*\.(js|css|woff|woff2|ttf|otf|eot|svg)$">
        Header set Cache-Control "public, max-age=31536000, immutable"
    </FilesMatch>

    ## Bilder
    ExpiresByType image/jpg "access plus 1 year"
    ExpiresByType image/jpeg "access plus 1 year"
    ExpiresByType image/gif "access plus 1 year"
    ExpiresByType image/png "access plus 1 year"
    ExpiresByType image/webp "access plus 1 year"
    ExpiresByType image/svg+xml "access plus 1 year"
    ExpiresByType image/x-icon "access plus 1 year"
    ExpiresByType image/avif "access plus 1 year"

    ## CSS und JavaScript (ohne Astro Build Assets)
    ExpiresByType text/css "access plus 1 month"
    ExpiresByType text/javascript "access plus 1 month"
    ExpiresByType application/javascript "access plus 1 month"

    ## Fonts
    ExpiresByType font/woff "access plus 1 year"
    ExpiresByType font/woff2 "access plus 1 year"
    ExpiresByType font/ttf "access plus 1 year"
    ExpiresByType font/otf "access plus 1 year"
    ExpiresByType application/font-woff "access plus 1 year"
    ExpiresByType application/font-woff2 "access plus 1 year"

    ## HTML - kurzes Caching für SSG-Seiten
    ExpiresByType text/html "access plus 1 hour"

    ## Andere
    ExpiresByType application/pdf "access plus 1 month"
    ExpiresByType application/json "access plus 0 seconds"
</IfModule>

##############################################
## Performance: GZIP Kompression
##############################################

<IfModule mod_deflate.c>
    ## Komprimiere HTML, CSS, JavaScript, Text, XML und Fonts
    AddOutputFilterByType DEFLATE application/javascript
    AddOutputFilterByType DEFLATE application/rss+xml
    AddOutputFilterByType DEFLATE application/vnd.ms-fontobject
    AddOutputFilterByType DEFLATE application/x-font
    AddOutputFilterByType DEFLATE application/x-font-opentype
    AddOutputFilterByType DEFLATE application/x-font-otf
    AddOutputFilterByType DEFLATE application/x-font-truetype
    AddOutputFilterByType DEFLATE application/x-font-ttf
    AddOutputFilterByType DEFLATE application/x-javascript
    AddOutputFilterByType DEFLATE application/xhtml+xml
    AddOutputFilterByType DEFLATE application/xml
    AddOutputFilterByType DEFLATE font/opentype
    AddOutputFilterByType DEFLATE font/otf
    AddOutputFilterByType DEFLATE font/ttf
    AddOutputFilterByType DEFLATE image/svg+xml
    AddOutputFilterByType DEFLATE image/x-icon
    AddOutputFilterByType DEFLATE text/css
    AddOutputFilterByType DEFLATE text/html
    AddOutputFilterByType DEFLATE text/javascript
    AddOutputFilterByType DEFLATE text/plain
    AddOutputFilterByType DEFLATE text/xml
    AddOutputFilterByType DEFLATE application/json

    ## Entferne Browser-Bugs (nur für alte Browser)
    BrowserMatch ^Mozilla/4 gzip-only-text/html
    BrowserMatch ^Mozilla/4\.0[678] no-gzip
    BrowserMatch \bMSIE !no-gzip !gzip-only-text/html
    Header append Vary User-Agent
</IfModule>

##############################################
## Security: Directory Browsing & File Protection
##############################################

## Verzeichnis Browsing deaktivieren
Options -Indexes

## Versteckte Dateien schützen
<FilesMatch "^\.">
    Order allow,deny
    Deny from all
</FilesMatch>

## Sensitive Dateien schützen
<FilesMatch "\.(htaccess|htpasswd|ini|log|sh|sql|env)$">
    Order allow,deny
    Deny from all
</FilesMatch>

## API-Dateien: Nur PHP-Dateien erlauben, keine .env direkt abrufbar
<Directory "/kunden/homepages/21/d4298613629/htdocs/api">
    <FilesMatch "\.env$">
        Order allow,deny
        Deny from all
    </FilesMatch>
</Directory>

##############################################
## URL Rewriting
##############################################

<IfModule mod_rewrite.c>
    RewriteEngine On

    ## HTTPS erzwingen (für Produktion aktiviert)
    RewriteCond %{HTTPS} off
    RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]

    ## WWW zu non-WWW (empfohlen für IDN-Domains)
    ## Einfache Regel: Entfernt www von JEDER Domain-Variante
    RewriteCond %{HTTP_HOST} ^www\.(.+)$ [NC]
    RewriteRule ^(.*)$ https://%1/$1 [R=301,L,NE]

    ## Astro.js: Trailing Slash Handling (falls nötig)
    ## Astro kann mit/ohne trailing slashes arbeiten - anpassen nach Bedarf
    # RewriteCond %{REQUEST_FILENAME} !-f
    # RewriteCond %{REQUEST_URI} !(.*)/$
    # RewriteRule ^(.*)$ https://%{HTTP_HOST}/$1/ [L,R=301]
</IfModule>

##############################################
## UTF-8 Encoding & MIME Types
##############################################

AddDefaultCharset UTF-8

<IfModule mod_mime.c>
    ## JavaScript
    AddType application/javascript js mjs
    AddType application/json json

    ## WebP & AVIF
    AddType image/webp webp
    AddType image/avif avif

    ## Fonts
    AddType font/ttf ttf
    AddType font/otf otf
    AddType font/woff woff
    AddType font/woff2 woff2
</IfModule>

##############################################
## Error Pages (optional)
##############################################

## Eigene Error-Pages (auskommentiert - bei Bedarf aktivieren)
# ErrorDocument 404 /404.html
# ErrorDocument 500 /500.html
