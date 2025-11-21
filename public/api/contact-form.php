<?php
/**
 * Contact Form Backend Proxy
 *
 * Proxies contact form submissions to Web3Forms API securely
 * Hides API key from frontend
 *
 * @author Wohlfuehlgesundheit - Holistische Darmtherapie
 * @version 1.0
 */

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/env-loader.php';
require_once __DIR__ . '/security.php';
require_once __DIR__ . '/phpmailer-helper.php';

// Load environment variables (auto-detects path)
loadEnv();

// Validate required environment variables
try {
    validateEnv([
        'ADMIN_EMAIL',
        'FROM_EMAIL',
        'ALLOWED_ORIGINS',
        'CSRF_SECRET',
        'SMTP_HOST',
        'SMTP_PORT',
        'SMTP_USERNAME',
        'SMTP_PASSWORD'
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Server-Konfigurationsfehler.'
    ], JSON_UNESCAPED_UNICODE);
    exit();
}

// Set JSON content type
header('Content-Type: application/json; charset=utf-8');

// Check CORS
checkCORS();

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Nur POST-Anfragen sind erlaubt.'
    ], JSON_UNESCAPED_UNICODE);
    exit();
}

// Check rate limiting (max 5 per hour for contact form)
checkRateLimit(5, 3600);

// Validate CSRF token
$csrfToken = $_POST['csrf_token'] ?? '';
if (!validateCSRFToken($csrfToken)) {
    echo json_encode([
        'success' => false,
        'message' => 'Sicherheitsvalidierung fehlgeschlagen. Bitte lade die Seite neu.'
    ], JSON_UNESCAPED_UNICODE);
    exit();
}

// Validate required fields
$requiredFields = ['name', 'email', 'message'];
foreach ($requiredFields as $field) {
    if (empty($_POST[$field])) {
        echo json_encode([
            'success' => false,
            'message' => 'Bitte fülle alle Pflichtfelder aus.'
        ], JSON_UNESCAPED_UNICODE);
        exit();
    }
}

// Validate and sanitize inputs
$name = validateText($_POST['name'], 100);
$email = validateEmail($_POST['email']);
$subject = !empty($_POST['subject']) ? validateText($_POST['subject'], 200) : 'Neue Nachricht von der Website';
$message = validateText($_POST['message'], 5000);

if ($name === false || $email === false || $message === false) {
    echo json_encode([
        'success' => false,
        'message' => 'Ungültige Eingabedaten. Bitte überprüfe deine Angaben.'
    ], JSON_UNESCAPED_UNICODE);
    exit();
}

// Prepare email
$adminEmail = env('ADMIN_EMAIL');
$emailSubject = 'Kontaktformular: ' . $subject;

// Email body
$emailBody = "
Neue Nachricht vom Kontaktformular
===================================

Von: {$name}
E-Mail: {$email}
Betreff: {$subject}

Nachricht:
{$message}

---
Gesendet: " . date('d.m.Y H:i:s') . "
IP: " . $_SERVER['REMOTE_ADDR'] . "
";

// Send email via PHPMailer (IONOS SMTP)
$mailSent = sendTextEmail($adminEmail, $emailSubject, $emailBody, $email);

if ($mailSent) {
    echo json_encode([
        'success' => true,
        'message' => 'Vielen Dank für deine Nachricht! Wir melden uns bald bei dir.'
    ], JSON_UNESCAPED_UNICODE);
} else {
    error_log('Contact form mail() failed for: ' . $email);
    echo json_encode([
        'success' => false,
        'message' => 'Fehler beim Senden. Bitte versuche es später erneut oder kontaktiere uns direkt per E-Mail.'
    ], JSON_UNESCAPED_UNICODE);
}
