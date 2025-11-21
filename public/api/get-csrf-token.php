<?php
/**
 * CSRF Token Generation API
 *
 * Returns a CSRF token for form submissions
 *
 * @author Wohlfuehlgesundheit - Holistische Darmtherapie
 * @version 1.0
 */

require_once __DIR__ . '/env-loader.php';
require_once __DIR__ . '/security.php';

// Load environment variables (auto-detects path)
loadEnv();

// Set JSON content type
header('Content-Type: application/json; charset=utf-8');

// Check CORS
checkCORS();

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Only allow GET requests
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Nur GET-Anfragen sind erlaubt.'
    ], JSON_UNESCAPED_UNICODE);
    exit();
}

// Generate and return CSRF token
$token = generateCSRFToken();

echo json_encode([
    'success' => true,
    'token' => $token
], JSON_UNESCAPED_UNICODE);
