<?php
/**
 * Web3Forms API Test Script
 *
 * Testet die Verbindung zur Web3Forms API
 */

require_once __DIR__ . '/env-loader.php';
loadEnv();

// Sicherheitscheck
$secret = $_GET['secret'] ?? '';
if ($secret !== 'debug2024') {
    http_response_code(403);
    die('Zugriff verweigert. Nutze: ?secret=debug2024');
}

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Web3Forms API Test</title>
    <style>
        body { font-family: monospace; padding: 20px; background: #1a1a1a; color: #00ff00; }
        .success { color: #00ff00; }
        .error { color: #ff0000; }
        .warning { color: #ffaa00; }
        .info { color: #00aaff; }
        pre { background: #000; padding: 10px; overflow-x: auto; }
        h2 { border-bottom: 2px solid #00ff00; padding-bottom: 5px; }
    </style>
</head>
<body>
    <h1>🔍 Web3Forms API Test</h1>

    <?php
    $apiKey = env('WEB3FORMS_API_KEY');

    echo "<h2>1️⃣ API-Key Check</h2>";
    if (empty($apiKey)) {
        echo "<p class='error'>❌ WEB3FORMS_API_KEY nicht gesetzt!</p>";
        exit;
    }

    echo "<p class='success'>✓ API-Key gefunden: " . substr($apiKey, 0, 8) . "...</p>";

    echo "<h2>2️⃣ Test-Request an Web3Forms</h2>";
    echo "<p class='info'>Sende Test-Nachricht an Web3Forms API...</p>";

    // Test-Daten
    $testData = [
        'access_key' => $apiKey,
        'name' => 'Test User (Debug Script)',
        'email' => 'test@example.com',
        'subject' => 'Test von Debug-Script',
        'message' => 'Dies ist eine Test-Nachricht vom Debug-Script. Zeitstempel: ' . date('Y-m-d H:i:s'),
        'from_name' => 'Wohlfuehlgesundheit Website'
    ];

    // Send to Web3Forms
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => 'https://api.web3forms.com/submit',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => http_build_query($testData),
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/x-www-form-urlencoded'
        ],
        CURLOPT_TIMEOUT => 30
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);

    echo "<h2>3️⃣ Response</h2>";

    if ($curlError) {
        echo "<p class='error'>❌ cURL Fehler: " . htmlspecialchars($curlError) . "</p>";
    } else {
        echo "<p class='info'>HTTP Status Code: <strong>{$httpCode}</strong></p>";

        if ($httpCode === 200) {
            $result = json_decode($response, true);

            if (isset($result['success']) && $result['success']) {
                echo "<p class='success'>✓ Nachricht erfolgreich gesendet!</p>";
                echo "<p class='info'>Web3Forms funktioniert korrekt!</p>";

                if (isset($result['message'])) {
                    echo "<p class='info'>API-Nachricht: " . htmlspecialchars($result['message']) . "</p>";
                }
            } else {
                echo "<p class='error'>❌ API gab 'success: false' zurück</p>";
                if (isset($result['message'])) {
                    echo "<p class='error'>Fehler: " . htmlspecialchars($result['message']) . "</p>";
                }
            }
        } else {
            echo "<p class='error'>❌ HTTP {$httpCode} - API-Anfrage fehlgeschlagen</p>";
        }

        echo "<h2>4️⃣ Rohe API-Antwort</h2>";
        echo "<pre>" . htmlspecialchars($response) . "</pre>";
    }

    echo "<h2>5️⃣ Gesendete Daten</h2>";
    echo "<pre>" . htmlspecialchars(print_r($testData, true)) . "</pre>";

    echo "<h2>6️⃣ Nächste Schritte</h2>";

    if ($httpCode === 200 && isset($result['success']) && $result['success']) {
        echo "<p class='success'>✓ Web3Forms API funktioniert!</p>";
        echo "<p class='warning'>⚠️ Falls das Kontaktformular trotzdem nicht funktioniert:</p>";
        echo "<ul>";
        echo "<li>Browser-Cache leeren</li>";
        echo "<li>CSRF-Token neu laden (Seite neu laden)</li>";
        echo "<li>Browser-Console auf Fehler prüfen</li>";
        echo "</ul>";
    } else {
        echo "<p class='error'>❌ Problem gefunden!</p>";
        echo "<ul>";
        echo "<li>API-Key überprüfen auf <a href='https://web3forms.com' style='color: #00aaff;'>web3forms.com</a></li>";
        echo "<li>Ggf. neuen API-Key erstellen</li>";
        echo "<li>In .env eintragen und Server neu starten</li>";
        echo "</ul>";
    }
    ?>

    <hr>
    <p class="warning">⚠️ Dieses Test-Script nach dem Debugging LÖSCHEN!</p>
</body>
</html>
