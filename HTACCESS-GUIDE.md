# .htaccess Guide für wohlfühlgesundheit.de

## 📁 Dateien

- `public/.htaccess` - Production-ready Apache-Konfiguration
- `public/_headers` - Astro.js Header-Datei (für Netlify/Vercel, nicht für Apache)

## ✅ Was wurde konfiguriert

### Security

- ✅ **Security Headers**: X-Frame-Options, X-Content-Type-Options, CSP, etc.
- ✅ **Content Security Policy**: Optimiert für Astro.js (ohne `unsafe-eval`)
- ✅ **File Protection**: .env, .htpasswd, sensible Dateien geschützt
- ✅ **Directory Browsing**: Deaktiviert

### Performance

- ✅ **Browser Caching**: Aggressive Caching für Astro Build-Assets (`/_astro/*`)
- ✅ **GZIP Compression**: Aktiviert für alle relevanten Dateitypen
- ✅ **Immutable Caching**: 1 Jahr für gehashte Assets

### SEO & Redirects

- ✅ **HTTPS Redirect**: Erzwingt HTTPS
- ✅ **WWW → non-WWW**: Beide Domain-Varianten (UTF-8 + Punycode)
- ✅ **IDN Support**: Funktioniert mit `wohlfühlgesundheit.de` und `xn--wohlfhlgesundheit-62b.de`

## 🔧 Wichtige Änderungen gegenüber dem Original

### ❌ Entfernt

1. **Passwortschutz** (war für Entwicklung)
   ```apache
   # AuthType Basic
   # AuthName "Geschützter Bereich"
   # AuthUserFile /kunden/homepages/21/d4298613629/htdocs/.htpasswd
   # Require valid-user
   ```

### ✅ Aktiviert

1. **HTTPS-Weiterleitung** (war auskommentiert)
2. **WWW-Weiterleitung** (war auskommentiert)

### 🔄 Verbessert

1. **CSP ohne `unsafe-eval`** - Astro 5 braucht das nicht
2. **Astro-spezifisches Caching** für `/_astro/*` Assets
3. **IDN-Domain Support** in Redirects

## 📝 Astro.js Besonderheiten

### `public/_headers` vs `.htaccess`

Astro generiert eine `_headers` Datei für moderne Hosting-Provider:

```
/_astro/*
  Cache-Control: public, max-age=31536000, immutable
```

Bei **IONOS/Apache** wird diese Datei ignoriert. Die `.htaccess` übernimmt diese Regeln:

```apache
<FilesMatch "^/_astro/.*\.(js|css|woff|woff2|ttf|otf|eot|svg)$">
    Header set Cache-Control "public, max-age=31536000, immutable"
</FilesMatch>
```

### Content Security Policy

Astro benötigt `'unsafe-inline'` für:
- **Islands Hydration** (client-side JavaScript)
- **View Transitions** (inline scripts für Navigation)

Aber **KEIN** `'unsafe-eval'` (wurde entfernt).

### Zoom Integration

CSP erlaubt `frame-src 'self' https://zoom.us` für Zoom-Meetings.

## 🔐 Passwortschutz reaktivieren (falls nötig)

Falls du die Seite später wieder schützen möchtest:

1. **Passwort-Hash generieren**:
   ```bash
   htpasswd -c .htpasswd username
   ```

2. **In .htaccess einfügen** (NACH den Security Headers):
   ```apache
   AuthType Basic
   AuthName "Geschützter Bereich"
   AuthUserFile /kunden/homepages/21/d4298613629/htdocs/.htpasswd
   Require valid-user
   ```

## 🧪 Testing

Nach dem Upload auf IONOS testen:

1. **HTTPS**: `http://wohlfühlgesundheit.de` → `https://wohlfühlgesundheit.de`
2. **WWW**: `https://www.wohlfühlgesundheit.de` → `https://wohlfühlgesundheit.de`
3. **Security Headers**: https://securityheaders.com
4. **Caching**: Browser DevTools → Network Tab → Cache-Control Header prüfen
5. **GZIP**: Browser DevTools → Network Tab → Content-Encoding: gzip

## 🐛 Troubleshooting

### CSP blockiert Skripte

Falls die CSP zu strikt ist und Fehler in der Console erscheinen:

```
Refused to execute inline script because it violates the following Content Security Policy directive...
```

**Lösung**: Passe die CSP an (z.B. `script-src` erweitern)

### Redirect Loop

Falls eine Redirect-Schleife entsteht:

**Prüfe**:
- Ist HTTPS im IONOS-Panel aktiviert?
- Gibt es doppelte Redirects (z.B. in Astro Config + .htaccess)?

**Lösung**:
```apache
# Temporär deaktivieren zum Testen
# RewriteCond %{HTTPS} off
# RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
```

### Astro-Assets werden nicht gecacht

**Prüfe** im Browser DevTools:
- URL muss mit `/_astro/` beginnen
- Header: `Cache-Control: public, max-age=31536000, immutable`

Falls nicht funktioniert, ist `mod_headers` möglicherweise nicht aktiviert (bei IONOS sollte es aber standardmäßig aktiv sein).

## 📚 Weitere Informationen

- [Apache mod_rewrite](https://httpd.apache.org/docs/current/mod/mod_rewrite.html)
- [Astro.js Headers](https://docs.astro.build/en/guides/middleware/#_headers)
- [IONOS .htaccess Guide](https://www.ionos.de/hilfe/hosting/htaccess/)
- [Content Security Policy](https://developer.mozilla.org/en-US/docs/Web/HTTP/CSP)
