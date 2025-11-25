<?php
/**
 * Debug Environment Variables
 *
 * ONLY works when DEBUG_MODE is enabled
 * Shows which environment variables are set (without revealing values)
 */

// Bootstrap
require_once __DIR__ . '/bootstrap.php';

// Set JSON content type
header('Content-Type: application/json; charset=utf-8');

// Only allow in debug mode
$debugMode = env('DEBUG_MODE') === 'true';
if (!$debugMode) {
    http_response_code(403);
    echo json_encode([
        'error' => 'Debug mode not enabled'
    ], JSON_UNESCAPED_UNICODE);
    exit();
}

// Check all required variables
$requiredVars = [
    'ZOOM_ACCOUNT_ID',
    'ZOOM_CLIENT_ID',
    'ZOOM_CLIENT_SECRET',
    'ADMIN_EMAIL',
    'FROM_EMAIL',
    'FROM_NAME',
    'SMTP_HOST',
    'SMTP_PORT',
    'SMTP_ENCRYPTION',
    'SMTP_USERNAME',
    'SMTP_PASSWORD',
    'ALLOWED_ORIGINS',
    'CSRF_SECRET',
    'RATE_LIMIT_MAX_REQUESTS',
    'RATE_LIMIT_TIME_WINDOW',
    'DEBUG_MODE'
];

$status = [];
$missing = [];
$hasValue = [];

foreach ($requiredVars as $var) {
    $value = env($var);
    $isset = $value !== null && $value !== '';

    $status[$var] = [
        'isset' => $isset,
        'length' => $isset ? strlen($value) : 0,
        'source' => $isset ? getVarSource($var) : 'not_found'
    ];

    if (!$isset) {
        $missing[] = $var;
    } else {
        $hasValue[] = $var;
    }
}

// Check .env file location
$envFileChecks = [
    __DIR__ . '/.env',
    __DIR__ . '/../.env',
    __DIR__ . '/../../.env',
];

$envFileStatus = [];
foreach ($envFileChecks as $path) {
    $envFileStatus[$path] = file_exists($path) ? 'exists' : 'not found';
}

echo json_encode([
    'success' => true,
    'server_info' => [
        'php_version' => PHP_VERSION,
        'document_root' => $_SERVER['DOCUMENT_ROOT'] ?? 'not set',
        'script_filename' => $_SERVER['SCRIPT_FILENAME'] ?? 'not set',
        'current_dir' => __DIR__
    ],
    'env_files' => $envFileStatus,
    'variables' => $status,
    'summary' => [
        'total' => count($requiredVars),
        'set' => count($hasValue),
        'missing' => count($missing),
        'missing_vars' => $missing
    ]
], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

function getVarSource($key) {
    if (getenv($key) !== false) return 'getenv';
    if (isset($_ENV[$key])) return '$_ENV';
    if (isset($_SERVER[$key])) return '$_SERVER';
    return 'unknown';
}
