<?php
/**
 * SMTP Debug Test - Detaillierte SMTP-Fehleranalyse
 * WICHTIG: Nach dem Debugging LÖSCHEN!
 */

// Load dependencies
$autoloadPaths = [
    __DIR__ . '/../../vendor/autoload.php',
    __DIR__ . '/../vendor/autoload.php',
    __DIR__ . '/vendor/autoload.php',
    $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php',
];

foreach ($autoloadPaths as $path) {
    if (file_exists($path)) {
        require_once $path;
        break;
    }
}

require_once __DIR__ . '/env-loader.php';
loadEnv();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <title>SMTP Debug Test</title>
    <style>
        body { font-family: monospace; padding: 20px; background: #1e1e1e; color: #d4d4d4; }
        .success { color: #4ec9b0; }
        .error { color: #f48771; }
        .warning { color: #dcdcaa; }
        pre { background: #252526; padding: 15px; border-left: 3px solid #007acc; overflow-x: auto; }
    </style>
</head>
<body>
    <h1>🔍 IONOS SMTP Debug Test</h1>

    <h2>Konfiguration:</h2>
    <pre><?php
echo "SMTP Host:       " . env('SMTP_HOST') . "\n";
echo "SMTP Port:       " . env('SMTP_PORT') . "\n";
echo "SMTP Encryption: " . env('SMTP_ENCRYPTION') . "\n";
echo "SMTP Username:   " . env('SMTP_USERNAME') . "\n";
echo "SMTP Password:   " . (env('SMTP_PASSWORD') ? '***gesetzt***' : 'NICHT GESETZT') . "\n";
echo "FROM Email:      " . env('FROM_EMAIL') . "\n";
echo "ADMIN Email:     " . env('ADMIN_EMAIL') . "\n";
    ?></pre>

    <h2>SMTP-Verbindungstest mit detailliertem Debug:</h2>
    <pre><?php

try {
    $mail = new PHPMailer(true);

    // SMTP-Einstellungen
    $mail->isSMTP();
    $mail->Host = env('SMTP_HOST');
    $mail->SMTPAuth = true;
    $mail->Username = env('SMTP_USERNAME');
    $mail->Password = env('SMTP_PASSWORD');
    $mail->SMTPSecure = env('SMTP_ENCRYPTION');
    $mail->Port = (int)env('SMTP_PORT');
    $mail->CharSet = 'UTF-8';

    // DETAILLIERTES DEBUGGING
    $mail->SMTPDebug = SMTP::DEBUG_SERVER;  // Zeigt Client -> Server und Server -> Client Kommunikation
    $mail->Debugoutput = function($str, $level) {
        echo htmlspecialchars($str);
    };

    // E-Mail Details
    $mail->setFrom(env('FROM_EMAIL'), env('FROM_NAME'));
    $mail->addAddress(env('ADMIN_EMAIL'));
    $mail->Subject = 'SMTP Test von Debug-Script';
    $mail->Body = 'Dies ist ein Test der SMTP-Verbindung mit PHPMailer.\n\nZeitstempel: ' . date('Y-m-d H:i:s');

    // Versuche zu senden
    echo "\n\n=== Versuche E-Mail zu senden ===\n\n";
    $result = $mail->send();

    if ($result) {
        echo "\n\n";
        echo '<span class="success">✓✓✓ E-MAIL ERFOLGREICH GESENDET! ✓✓✓</span>' . "\n";
        echo "\nPrüfe dein Postfach: " . env('ADMIN_EMAIL');
    }

} catch (Exception $e) {
    echo "\n\n";
    echo '<span class="error">❌ FEHLER BEIM SENDEN:</span>' . "\n";
    echo htmlspecialchars($e->getMessage()) . "\n\n";
    echo '<span class="warning">Mögliche Ursachen:</span>' . "\n";
    echo "1. Falsches SMTP-Passwort\n";
    echo "2. SMTP-Username muss die vollständige E-Mail-Adresse sein\n";
    echo "3. IONOS blockiert externe SMTP-Verbindungen\n";
    echo "4. Port 587 ist blockiert (versuche Port 465 mit ssl)\n";
    echo "5. Die E-Mail-Adresse existiert nicht im IONOS-Account\n";
}

    ?></pre>

    <hr>
    <p><strong>Nächste Schritte wenn Fehler auftreten:</strong></p>
    <ol>
        <li>Prüfe das SMTP-Passwort in der .env Datei</li>
        <li>Versuche Port 465 mit SSL statt 587 mit TLS</li>
        <li>Stelle sicher, dass die E-Mail-Adresse im IONOS Email-Center existiert</li>
        <li>Prüfe ob 2FA aktiviert ist (dann brauchst du ein App-Passwort)</li>
    </ol>

    <p><em>⚠️ Diese Datei nach dem Debugging LÖSCHEN!</em></p>
</body>
</html>
