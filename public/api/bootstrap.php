<?php
/**
 * Bootstrap File - Centralized Initialization
 *
 * Loads Composer autoloader and common dependencies
 * Eliminates code duplication across API endpoints
 *
 * @author Wohlfuehlgesundheit - Holistische Darmtherapie
 * @version 1.0
 */

// ============================================================================
// COMPOSER AUTOLOADER
// ============================================================================

// Try multiple paths for IONOS compatibility
$autoloadPaths = [
    __DIR__ . '/../../vendor/autoload.php',              // /htdocs/public/api -> /htdocs/vendor
    __DIR__ . '/../vendor/autoload.php',                 // /htdocs/api -> /htdocs/vendor
    __DIR__ . '/vendor/autoload.php',                    // /htdocs/api/vendor
    $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php',  // Document root
    $_SERVER['DOCUMENT_ROOT'] . '/../vendor/autoload.php', // Parent of document root
    dirname(dirname(__DIR__)) . '/vendor/autoload.php',  // 2 levels up
    dirname(__DIR__) . '/vendor/autoload.php',            // 1 level up
];

$autoloadLoaded = false;
foreach ($autoloadPaths as $autoloadPath) {
    if (file_exists($autoloadPath)) {
        require_once $autoloadPath;
        $autoloadLoaded = true;
        break;
    }
}

if (!$autoloadLoaded) {
    http_response_code(500);

    // Only log details in debug mode
    $debugMode = getenv('DEBUG_MODE') === 'true' || (isset($_ENV['DEBUG_MODE']) && $_ENV['DEBUG_MODE'] === 'true');
    if ($debugMode) {
        error_log('Composer autoload not found. Paths tried: ' . implode(', ', $autoloadPaths));
        error_log('__DIR__ = ' . __DIR__);
        error_log('DOCUMENT_ROOT = ' . ($_SERVER['DOCUMENT_ROOT'] ?? 'not set'));
    }

    echo json_encode([
        'success' => false,
        'message' => 'Server-Konfigurationsfehler: Composer autoload nicht gefunden.'
    ], JSON_UNESCAPED_UNICODE);
    exit();
}

// ============================================================================
// LOAD CORE DEPENDENCIES
// ============================================================================

require_once __DIR__ . '/config.php';           // Configuration constants
require_once __DIR__ . '/env-loader.php';       // Environment variable loader
require_once __DIR__ . '/security.php';         // Security functions
require_once __DIR__ . '/phpmailer-helper.php'; // Email helper

// ============================================================================
// LOAD ENVIRONMENT VARIABLES
// ============================================================================

if (!loadEnv()) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Server-Konfigurationsfehler. Bitte kontaktiere den Administrator.'
    ], JSON_UNESCAPED_UNICODE);
    exit();
}
