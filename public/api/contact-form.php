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

require_once __DIR__ . '/env-loader.php';
require_once __DIR__ . '/security.php';

// Load environment variables
loadEnv(__DIR__ . '/../.env');

// Validate required environment variables
try {
    validateEnv(['WEB3FORMS_API_KEY', 'ADMIN_EMAIL', 'ALLOWED_ORIGINS', 'CSRF_SECRET']);
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
$subject = !empty($_POST['subject']) ? validateText($_POST['subject'], 200) : '';
$message = validateText($_POST['message'], 5000);

if ($name === false || $email === false || $message === false) {
    echo json_encode([
        'success' => false,
        'message' => 'Ungültige Eingabedaten. Bitte überprüfe deine Angaben.'
    ], JSON_UNESCAPED_UNICODE);
    exit();
}

// Prepare data for Web3Forms
$web3formsData = [
    'access_key' => env('WEB3FORMS_API_KEY'),
    'name' => $name,
    'email' => $email,
    'subject' => $subject ?: 'Neue Nachricht von der Website',
    'message' => $message,
    'from_name' => 'Wohlfuehlgesundheit Website',
    'replyto' => $email,
    'redirect' => 'https://wohlfuehlgesundheit.de/danke'
];

// Send to Web3Forms API
$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL => 'https://api.web3forms.com/submit',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => http_build_query($web3formsData),
    CURLOPT_HTTPHEADER => [
        'Content-Type: application/x-www-form-urlencoded'
    ],
    CURLOPT_TIMEOUT => 30
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

if (curl_errno($ch)) {
    $error = curl_error($ch);
    curl_close($ch);
    error_log('Web3Forms API Error: ' . $error);

    echo json_encode([
        'success' => false,
        'message' => 'Verbindungsfehler. Bitte versuche es später erneut.'
    ], JSON_UNESCAPED_UNICODE);
    exit();
}

curl_close($ch);

// Parse response
$result = json_decode($response, true);

if ($httpCode === 200 && isset($result['success']) && $result['success']) {
    echo json_encode([
        'success' => true,
        'message' => 'Vielen Dank für deine Nachricht! Wir melden uns bald bei dir.'
    ], JSON_UNESCAPED_UNICODE);
} else {
    error_log('Web3Forms API Error: ' . $response);
    echo json_encode([
        'success' => false,
        'message' => 'Fehler beim Senden. Bitte versuche es später erneut.'
    ], JSON_UNESCAPED_UNICODE);
}
