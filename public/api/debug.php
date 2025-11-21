<?php
/**
 * Debug-Script für API-Probleme
 *
 * Prüft:
 * - .env-Dateipfade
 * - PHP-Konfiguration
 * - Umgebungsvariablen
 * - E-Mail-Funktionalität
 * - Zoom-API-Verbindung
 *
 * WICHTIG: Nach dem Debugging LÖSCHEN oder umbenennen!
 */

// Sicherheitscheck - nur von localhost oder mit secret parameter
$allowedIPs = ['127.0.0.1', '::1'];
$secret = $_GET['secret'] ?? '';
$expectedSecret = 'debug2024'; // Ändere dies zu einem sicheren Wert

if (!in_array($_SERVER['REMOTE_ADDR'], $allowedIPs) && $secret !== $expectedSecret) {
    http_response_code(403);
    die('Zugriff verweigert. Nutze: ?secret=debug2024');
}

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>API Debug Report</title>
    <style>
        body { font-family: monospace; padding: 20px; background: #1a1a1a; color: #00ff00; }
        .section { background: #2a2a2a; padding: 15px; margin: 10px 0; border-left: 4px solid #00ff00; }
        .success { color: #00ff00; }
        .error { color: #ff0000; }
        .warning { color: #ffaa00; }
        .info { color: #00aaff; }
        h2 { color: #00ff00; border-bottom: 2px solid #00ff00; padding-bottom: 5px; }
        pre { background: #000; padding: 10px; overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; }
        td { padding: 5px; border: 1px solid #444; }
        td:first-child { font-weight: bold; width: 30%; }
    </style>
</head>
<body>
    <h1>🔍 API Debug Report</h1>
    <p class="warning">⚠️ WICHTIG: Lösche diese Datei nach dem Debugging!</p>

    <?php
    // ========================================================================
    // 1. PFAD-INFORMATIONEN
    // ========================================================================
    ?>
    <div class="section">
        <h2>1️⃣ Pfad-Informationen</h2>
        <table>
            <tr>
                <td>__DIR__</td>
                <td><?= __DIR__ ?></td>
            </tr>
            <tr>
                <td>__FILE__</td>
                <td><?= __FILE__ ?></td>
            </tr>
            <tr>
                <td>DOCUMENT_ROOT</td>
                <td><?= $_SERVER['DOCUMENT_ROOT'] ?? 'nicht gesetzt' ?></td>
            </tr>
            <tr>
                <td>SCRIPT_FILENAME</td>
                <td><?= $_SERVER['SCRIPT_FILENAME'] ?? 'nicht gesetzt' ?></td>
            </tr>
        </table>
    </div>

    <?php
    // ========================================================================
    // 2. .ENV-DATEI SUCHEN
    // ========================================================================
    ?>
    <div class="section">
        <h2>2️⃣ .env-Datei Suche</h2>
        <?php
        $envPaths = [
            __DIR__ . '/.env',              // /public/api/.env
            __DIR__ . '/../.env',           // /public/.env (aktueller Code)
            __DIR__ . '/../../.env',        // / (Projekt-Root)
            $_SERVER['DOCUMENT_ROOT'] . '/.env',
            $_SERVER['DOCUMENT_ROOT'] . '/api/.env',
        ];

        $foundEnv = false;
        echo "<table>";
        foreach ($envPaths as $path) {
            $exists = file_exists($path);
            $readable = $exists && is_readable($path);

            echo "<tr>";
            echo "<td>" . htmlspecialchars($path) . "</td>";
            echo "<td>";

            if ($exists && $readable) {
                echo "<span class='success'>✓ Gefunden & lesbar</span>";
                $foundEnv = $path;

                // Dateigröße
                $size = filesize($path);
                echo " (Größe: {$size} Bytes)";

                // Erste Zeilen anzeigen (ohne Werte!)
                $lines = file($path, FILE_IGNORE_NEW_LINES);
                $keyCount = 0;
                foreach ($lines as $line) {
                    if (strpos(trim($line), '=') !== false && strpos(trim($line), '#') !== 0) {
                        $keyCount++;
                    }
                }
                echo " - {$keyCount} Variablen gefunden";

            } elseif ($exists) {
                echo "<span class='error'>✗ Gefunden aber nicht lesbar (Berechtigung?)</span>";
            } else {
                echo "<span class='warning'>✗ Nicht gefunden</span>";
            }

            echo "</td>";
            echo "</tr>";
        }
        echo "</table>";

        if (!$foundEnv) {
            echo "<p class='error'>❌ Keine .env-Datei gefunden!</p>";
        } else {
            echo "<p class='success'>✓ .env-Datei gefunden: {$foundEnv}</p>";
        }
        ?>
    </div>

    <?php
    // ========================================================================
    // 3. .ENV LADEN UND TESTEN
    // ========================================================================
    ?>
    <div class="section">
        <h2>3️⃣ .env-Datei laden</h2>
        <?php
        require_once __DIR__ . '/env-loader.php';

        // Versuche alle möglichen Pfade
        $loaded = false;
        foreach ($envPaths as $path) {
            if (file_exists($path)) {
                echo "<p class='info'>Versuche zu laden: {$path}</p>";
                if (loadEnv($path)) {
                    echo "<p class='success'>✓ Erfolgreich geladen!</p>";
                    $loaded = true;
                    break;
                } else {
                    echo "<p class='error'>✗ Laden fehlgeschlagen</p>";
                }
            }
        }

        if (!$loaded) {
            echo "<p class='error'>❌ Keine .env-Datei konnte geladen werden!</p>";
        }
        ?>
    </div>

    <?php
    // ========================================================================
    // 4. UMGEBUNGSVARIABLEN PRÜFEN
    // ========================================================================
    ?>
    <div class="section">
        <h2>4️⃣ Umgebungsvariablen</h2>
        <?php
        $requiredVars = [
            'ZOOM_ACCOUNT_ID',
            'ZOOM_CLIENT_ID',
            'ZOOM_CLIENT_SECRET',
            'ADMIN_EMAIL',
            'FROM_EMAIL',
            'FROM_NAME',
            'ALLOWED_ORIGINS',
            'CSRF_SECRET',
            'WEB3FORMS_API_KEY',
        ];

        echo "<table>";
        foreach ($requiredVars as $var) {
            $value = env($var);
            $isset = !empty($value);

            echo "<tr>";
            echo "<td>{$var}</td>";
            echo "<td>";

            if ($isset) {
                // Wert maskieren (nur ersten und letzten Zeichen zeigen)
                $maskedValue = strlen($value) > 8
                    ? substr($value, 0, 4) . '...' . substr($value, -4)
                    : '***';
                echo "<span class='success'>✓ Gesetzt</span> ({$maskedValue})";
            } else {
                echo "<span class='error'>✗ Nicht gesetzt oder leer</span>";
            }

            echo "</td>";
            echo "</tr>";
        }
        echo "</table>";
        ?>
    </div>

    <?php
    // ========================================================================
    // 5. PHP-KONFIGURATION
    // ========================================================================
    ?>
    <div class="section">
        <h2>5️⃣ PHP-Konfiguration</h2>
        <table>
            <tr>
                <td>PHP Version</td>
                <td><?= phpversion() ?></td>
            </tr>
            <tr>
                <td>allow_url_fopen</td>
                <td class="<?= ini_get('allow_url_fopen') ? 'success' : 'error' ?>">
                    <?= ini_get('allow_url_fopen') ? '✓ Aktiviert' : '✗ Deaktiviert' ?>
                </td>
            </tr>
            <tr>
                <td>cURL Extension</td>
                <td class="<?= extension_loaded('curl') ? 'success' : 'error' ?>">
                    <?= extension_loaded('curl') ? '✓ Verfügbar' : '✗ Nicht verfügbar' ?>
                </td>
            </tr>
            <tr>
                <td>OpenSSL Extension</td>
                <td class="<?= extension_loaded('openssl') ? 'success' : 'error' ?>">
                    <?= extension_loaded('openssl') ? '✓ Verfügbar' : '✗ Nicht verfügbar' ?>
                </td>
            </tr>
            <tr>
                <td>mail() Funktion</td>
                <td class="<?= function_exists('mail') ? 'success' : 'error' ?>">
                    <?= function_exists('mail') ? '✓ Verfügbar' : '✗ Nicht verfügbar' ?>
                </td>
            </tr>
            <tr>
                <td>sendmail_path</td>
                <td><?= ini_get('sendmail_path') ?: 'nicht konfiguriert' ?></td>
            </tr>
            <tr>
                <td>SMTP (Windows)</td>
                <td><?= ini_get('SMTP') ?: 'nicht konfiguriert (Linux/Unix)' ?></td>
            </tr>
            <tr>
                <td>error_log</td>
                <td><?= ini_get('error_log') ?: 'nicht konfiguriert' ?></td>
            </tr>
            <tr>
                <td>display_errors</td>
                <td class="<?= ini_get('display_errors') ? 'warning' : 'success' ?>">
                    <?= ini_get('display_errors') ? '⚠️ Aktiviert (sollte in Production aus sein!)' : '✓ Deaktiviert' ?>
                </td>
            </tr>
        </table>
    </div>

    <?php
    // ========================================================================
    // 6. ZOOM API TESTEN
    // ========================================================================
    ?>
    <div class="section">
        <h2>6️⃣ Zoom API Verbindung</h2>
        <?php
        if (env('ZOOM_ACCOUNT_ID') && env('ZOOM_CLIENT_ID') && env('ZOOM_CLIENT_SECRET')) {
            $accountId = env('ZOOM_ACCOUNT_ID');
            $clientId = env('ZOOM_CLIENT_ID');
            $clientSecret = env('ZOOM_CLIENT_SECRET');

            $auth = base64_encode($clientId . ':' . $clientSecret);
            $url = 'https://zoom.us/oauth/token?grant_type=account_credentials&account_id=' . $accountId;

            echo "<p class='info'>Teste Zoom OAuth...</p>";

            $ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_HTTPHEADER => [
                    'Authorization: Basic ' . $auth,
                    'Content-Type: application/x-www-form-urlencoded'
                ],
                CURLOPT_TIMEOUT => 10
            ]);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);
            curl_close($ch);

            if ($curlError) {
                echo "<p class='error'>❌ cURL Fehler: {$curlError}</p>";
            } else {
                if ($httpCode === 200) {
                    $data = json_decode($response, true);
                    if (isset($data['access_token'])) {
                        echo "<p class='success'>✓ Zoom API Authentifizierung erfolgreich!</p>";
                        echo "<p class='info'>Access Token erhalten (läuft ab in {$data['expires_in']} Sekunden)</p>";
                    } else {
                        echo "<p class='error'>❌ Keine Access Token in Antwort</p>";
                        echo "<pre>" . htmlspecialchars($response) . "</pre>";
                    }
                } else {
                    echo "<p class='error'>❌ HTTP {$httpCode} - Authentifizierung fehlgeschlagen</p>";
                    echo "<pre>" . htmlspecialchars($response) . "</pre>";

                    if ($httpCode === 401) {
                        echo "<p class='warning'>⚠️ Ungültige Credentials (ZOOM_ACCOUNT_ID, ZOOM_CLIENT_ID oder ZOOM_CLIENT_SECRET falsch)</p>";
                    }
                }
            }
        } else {
            echo "<p class='error'>❌ Zoom-Credentials nicht vollständig konfiguriert</p>";
        }
        ?>
    </div>

    <?php
    // ========================================================================
    // 7. E-MAIL TEST
    // ========================================================================
    ?>
    <div class="section">
        <h2>7️⃣ E-Mail Konfiguration</h2>
        <?php
        if (env('ADMIN_EMAIL') && env('FROM_EMAIL')) {
            echo "<table>";
            echo "<tr><td>Admin E-Mail</td><td>" . htmlspecialchars(env('ADMIN_EMAIL')) . "</td></tr>";
            echo "<tr><td>From E-Mail</td><td>" . htmlspecialchars(env('FROM_EMAIL')) . "</td></tr>";
            echo "<tr><td>From Name</td><td>" . htmlspecialchars(env('FROM_NAME', 'nicht gesetzt')) . "</td></tr>";
            echo "</table>";

            if (isset($_GET['sendtest']) && $_GET['sendtest'] === '1') {
                echo "<p class='info'>Sende Test-E-Mail...</p>";

                $to = env('ADMIN_EMAIL');
                $subject = 'Test-E-Mail vom Debug-Script';
                $message = "Dies ist eine Test-E-Mail vom Debug-Script.\n\n";
                $message .= "Zeitstempel: " . date('Y-m-d H:i:s') . "\n";
                $message .= "Server: " . $_SERVER['SERVER_NAME'] . "\n";

                $headers = [
                    'From: ' . env('FROM_EMAIL'),
                    'Reply-To: ' . env('FROM_EMAIL'),
                    'X-Mailer: PHP/' . phpversion()
                ];

                $result = mail($to, $subject, $message, implode("\r\n", $headers));

                if ($result) {
                    echo "<p class='success'>✓ mail() Funktion gab TRUE zurück</p>";
                    echo "<p class='warning'>⚠️ Das bedeutet NICHT, dass die E-Mail angekommen ist!</p>";
                    echo "<p class='info'>Prüfe dein E-Mail-Postfach (auch Spam): {$to}</p>";
                } else {
                    echo "<p class='error'>❌ mail() Funktion gab FALSE zurück</p>";
                    echo "<p class='warning'>⚠️ PHP Mail-Konfiguration überprüfen!</p>";
                }
            } else {
                echo "<p class='info'><a href='?secret={$secret}&sendtest=1' style='color: #00aaff;'>→ Klicke hier um Test-E-Mail zu senden</a></p>";
            }
        } else {
            echo "<p class='error'>❌ E-Mail-Adressen nicht konfiguriert</p>";
        }
        ?>
    </div>

    <?php
    // ========================================================================
    // 8. CORS & SECURITY
    // ========================================================================
    ?>
    <div class="section">
        <h2>8️⃣ CORS & Security</h2>
        <?php
        $allowedOrigins = env('ALLOWED_ORIGINS', 'nicht gesetzt');
        $hasUTF8 = strpos($allowedOrigins, 'wohlfühlgesundheit.de') !== false;
        $hasPunycode = strpos($allowedOrigins, 'xn--wohlfhlgesundheit-62b.de') !== false;

        echo "<table>";
        echo "<tr><td>ALLOWED_ORIGINS</td><td>" . htmlspecialchars($allowedOrigins) . "</td></tr>";

        // IDN-Check
        echo "<tr><td>IDN-Domain Check</td><td>";
        if ($hasUTF8 && $hasPunycode) {
            echo "<span class='success'>✓ Beide Varianten vorhanden (UTF-8 + Punycode)</span>";
        } elseif ($hasUTF8 || $hasPunycode) {
            echo "<span class='warning'>⚠️ Nur eine Variante vorhanden!</span><br>";
            echo "<small>Empfehlung: Beide angeben für maximale Kompatibilität:<br>";
            echo "wohlfühlgesundheit.de UND xn--wohlfhlgesundheit-62b.de</small>";
        } else {
            echo "<span class='info'>ℹ️ Keine IDN-Domain erkannt</span>";
        }
        echo "</td></tr>";

        echo "<tr><td>CSRF_SECRET</td><td>" . (env('CSRF_SECRET') ? '<span class="success">✓ Gesetzt</span>' : '<span class="error">✗ Nicht gesetzt</span>') . "</td></tr>";
        echo "<tr><td>Rate Limit Verzeichnis</td>";

        $rateLimitDir = __DIR__ . '/../_rate_limit';
        if (is_dir($rateLimitDir)) {
            $writable = is_writable($rateLimitDir);
            echo "<td class='" . ($writable ? 'success' : 'error') . "'>";
            echo $writable ? '✓ Existiert & beschreibbar' : '✗ Existiert aber nicht beschreibbar';
            echo "</td>";
        } else {
            echo "<td class='warning'>⚠️ Existiert nicht (wird beim ersten Request erstellt)</td>";
        }
        echo "</tr>";
        echo "</table>";

        // IDN-Hinweis
        if (!($hasUTF8 && $hasPunycode)) {
            echo "<div style='background: #2a2a2a; padding: 10px; margin-top: 10px; border-left: 4px solid #ffaa00;'>";
            echo "<p class='warning'><strong>💡 WICHTIG:</strong> Diese Domain nutzt Umlaute (IDN)!</p>";
            echo "<p class='info'>Für optimale CORS-Kompatibilität beide Varianten in ALLOWED_ORIGINS eintragen:</p>";
            echo "<pre style='background: #000; padding: 5px;'>";
            echo "ALLOWED_ORIGINS=https://wohlfühlgesundheit.de,https://xn--wohlfhlgesundheit-62b.de";
            echo "</pre>";
            echo "<p class='info'>→ Siehe IDN-DOMAIN.md für Details</p>";
            echo "</div>";
        }
        ?>
    </div>

    <?php
    // ========================================================================
    // 9. EMPFEHLUNGEN
    // ========================================================================
    ?>
    <div class="section">
        <h2>9️⃣ Empfehlungen</h2>
        <ul>
            <?php if (!$foundEnv): ?>
            <li class="error">❌ .env-Datei erstellen und korrekt platzieren</li>
            <?php endif; ?>

            <?php if (!env('ZOOM_ACCOUNT_ID')): ?>
            <li class="error">❌ ZOOM_ACCOUNT_ID in .env setzen</li>
            <?php endif; ?>

            <?php if (!env('ADMIN_EMAIL')): ?>
            <li class="error">❌ ADMIN_EMAIL in .env setzen</li>
            <?php endif; ?>

            <?php if (ini_get('display_errors')): ?>
            <li class="warning">⚠️ display_errors sollte in Production auf Off gesetzt werden</li>
            <?php endif; ?>

            <?php if (!ini_get('error_log')): ?>
            <li class="warning">⚠️ error_log-Pfad konfigurieren für besseres Debugging</li>
            <?php endif; ?>

            <li class="error">❌ <strong>Diese debug.php Datei nach dem Debugging LÖSCHEN!</strong></li>
        </ul>
    </div>

    <div class="section">
        <p class="warning">⚠️ <strong>WICHTIG:</strong> Lösche diese Datei nach dem Debugging oder benenne sie um!</p>
        <p class="info">Bei Fragen: Dokumentation in README.md prüfen</p>
    </div>

</body>
</html>
