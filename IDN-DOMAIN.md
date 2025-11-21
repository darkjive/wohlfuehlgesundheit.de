# 🌐 INTERNATIONALISIERTE DOMAIN (IDN)

## Domain mit Umlaut

Diese Website verwendet eine **Internationalisierte Domain** mit Umlaut:

```
wohlfühlgesundheit.de
```

## Was ist Punycode?

Internationalisierte Domains mit Umlauten, Akzenten oder anderen Nicht-ASCII-Zeichen werden technisch als **Punycode** kodiert:

```
Original:  wohlfühlgesundheit.de
Punycode:  xn--wohlfhlgesundheit-62b.de
```

→ Siehe: https://de.wikipedia.org/wiki/Internationalisierter_Domainname

---

## 🔧 TECHNISCHE VERWENDUNG

### Browser & Frontend

Moderne Browser konvertieren **automatisch** zwischen beiden Schreibweisen:

```javascript
// Beide funktionieren!
https://wohlfühlgesundheit.de         ✅
https://xn--wohlfhlgesundheit-62b.de  ✅
```

### Backend & API (CORS, .env)

Für CORS und API-Konfiguration **BEIDE Varianten** eintragen:

```env
# .env - ALLOWED_ORIGINS
ALLOWED_ORIGINS=https://wohlfühlgesundheit.de,https://xn--wohlfhlgesundheit-62b.de,https://www.wohlfühlgesundheit.de,https://www.xn--wohlfhlgesundheit-62b.de
```

**Warum?**
- Manche Browser senden die UTF-8-Variante (`wohlfühlgesundheit.de`)
- Andere senden die Punycode-Variante (`xn--wohlfhlgesundheit-62b.de`)
- Für sichere CORS-Prüfung: **beide erlauben**

---

## 📧 E-MAIL-ADRESSEN

E-Mail-Adressen mit IDN-Domains:

```
steffi@wohlfühlgesundheit.de        → Funktioniert (moderne Mailserver)
steffi@xn--wohlfhlgesundheit-62b.de → Funktioniert immer
```

**Empfehlung:**
- In .env: Punycode verwenden für maximale Kompatibilität
- In UI/Frontend: Mit Umlaut anzeigen (benutzerfreundlich)

```env
# .env
FROM_EMAIL=noreply@xn--wohlfhlgesundheit-62b.de
ADMIN_EMAIL=steffi@xn--wohlfhlgesundheit-62b.de
```

---

## 🌐 DNS & SSL

### DNS-Konfiguration

Bei deinem Domain-Registrar (z.B. IONOS, Strato):
- Beide Schreibweisen zeigen auf die gleiche IP
- DNS-Server konvertieren automatisch

### SSL-Zertifikat

Moderne SSL-Zertifikate unterstützen beide:
```
Common Name: wohlfühlgesundheit.de
SANs:
  - wohlfühlgesundheit.de
  - www.wohlfühlgesundheit.de
  - xn--wohlfhlgesundheit-62b.de
  - www.xn--wohlfhlgesundheit-62b.de
```

---

## ⚙️ ASTRO KONFIGURATION

### src/config.yaml

```yaml
site:
  site: 'https://wohlfühlgesundheit.de'  # Mit Umlaut OK!
```

**Astro konvertiert automatisch** zu Punycode wo nötig.

---

## 🧪 TESTING

### Browser-Test

1. **UTF-8-Variante:**
   ```
   https://wohlfühlgesundheit.de
   ```

2. **Punycode-Variante:**
   ```
   https://xn--wohlfhlgesundheit-62b.de
   ```

Beide sollten zur gleichen Website führen!

### CORS-Test

```javascript
// Im Browser-Console auf deiner Website:
fetch('/api/anamnese-booking.php', {
  method: 'POST',
  headers: {
    'Origin': window.location.origin
  }
});

// Prüfe Network-Tab:
// Request Headers → Origin: sollte gesetzt sein
// Response Headers → Access-Control-Allow-Origin: sollte matchen
```

---

## 🔍 DEBUGGING

### Welche Variante sendet der Browser?

```javascript
// Browser-Console
console.log(window.location.hostname);
// → Zeigt welche Variante der Browser verwendet
```

### CORS-Fehler?

**Symptom:**
```
Access to fetch at '...' from origin 'https://wohlfühlgesundheit.de'
has been blocked by CORS policy
```

**Lösung:**
Beide Varianten in ALLOWED_ORIGINS:
```env
ALLOWED_ORIGINS=https://wohlfühlgesundheit.de,https://xn--wohlfhlgesundheit-62b.de
```

---

## 📝 CHECKLISTE

### Bei Domain-Setup:

- [ ] DNS-Records für beide Varianten
- [ ] SSL-Zertifikat für beide Varianten
- [ ] .env ALLOWED_ORIGINS mit beiden Varianten
- [ ] Teste beide URLs im Browser
- [ ] Teste CORS mit beiden Varianten

### Bei E-Mail-Problemen:

- [ ] Nutze Punycode in FROM_EMAIL
- [ ] Teste E-Mail-Versand
- [ ] Prüfe SPF/DKIM-Records

---

## 📚 WEITERE INFORMATIONEN

### Tools

**Punycode-Konverter online:**
- https://www.punycoder.com/
- https://www.charset.org/punycode

**Node.js:**
```javascript
const { domainToASCII, domainToUnicode } = require('url');

console.log(domainToASCII('wohlfühlgesundheit.de'));
// → xn--wohlfhlgesundheit-62b.de

console.log(domainToUnicode('xn--wohlfhlgesundheit-62b.de'));
// → wohlfühlgesundheit.de
```

**PHP:**
```php
<?php
echo idn_to_ascii('wohlfühlgesundheit.de');
// → xn--wohlfhlgesundheit-62b.de

echo idn_to_utf8('xn--wohlfhlgesundheit-62b.de');
// → wohlfühlgesundheit.de
```

### Browser-Support

✅ **Vollständig unterstützt:**
- Chrome/Edge 90+
- Firefox 88+
- Safari 14+

⚠️ **Ältere Browser:**
- IE 11 und älter: Nur Punycode
- Alte Android-Browser: Möglicherweise nur Punycode

---

## 🎯 EMPFEHLUNG

### Für maximale Kompatibilität:

1. **Frontend/UI:**
   - Zeige immer mit Umlaut: `wohlfühlgesundheit.de`
   - Benutzerfreundlich!

2. **Backend/.env:**
   - Nutze Punycode: `xn--wohlfhlgesundheit-62b.de`
   - 100% kompatibel!

3. **CORS (ALLOWED_ORIGINS):**
   - **Beide** Varianten erlauben
   - Sicher!

---

**Stand:** November 2025
