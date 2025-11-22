<?php
/**
 * Simple .env File Loader
 *
 * Loads environment variables from .env file
 * No external dependencies required
 *
 * @author Wohlfuehlgesundheit - Holistische Darmtherapie
 * @version 1.0
 */

/**
 * Load environment variables from .env file
 * Tries multiple paths automatically if no specific path is provided
 *
 * @param string|null $path Path to .env file (optional)
 * @return bool Success status
 */
function loadEnv($path = null) {
    // If specific path provided, use it
    if ($path !== null) {
        if (!file_exists($path)) {
            error_log('.env file not found at: ' . $path);
            return false;
        }
        return loadEnvFromFile($path);
    }

    // Try multiple common paths
    $possiblePaths = [
        __DIR__ . '/.env',              // /public/api/.env (same directory)
        __DIR__ . '/../.env',           // /public/.env (one level up)
        __DIR__ . '/../../.env',        // / (project root, two levels up)
        $_SERVER['DOCUMENT_ROOT'] . '/.env',          // Document root
        $_SERVER['DOCUMENT_ROOT'] . '/api/.env',      // Document root + /api/
    ];

    foreach ($possiblePaths as $tryPath) {
        if (file_exists($tryPath)) {
            // Only log path in debug mode to avoid exposing server structure
            if (getenv('DEBUG_MODE') === 'true' || (isset($_ENV['DEBUG_MODE']) && $_ENV['DEBUG_MODE'] === 'true')) {
                error_log('Loading .env from: ' . $tryPath);
            }
            return loadEnvFromFile($tryPath);
        }
    }

    // Only log detailed error in debug mode
    if (getenv('DEBUG_MODE') === 'true' || (isset($_ENV['DEBUG_MODE']) && $_ENV['DEBUG_MODE'] === 'true')) {
        error_log('.env file not found in any of the standard locations');
    } else {
        error_log('.env file not found');
    }
    return false;
}

/**
 * Actually load the .env file
 *
 * @param string $path Path to .env file
 * @return bool Success status
 */
function loadEnvFromFile($path) {
    // Check if .env file exists
    if (!file_exists($path)) {
        // Only log path in debug mode
        if (getenv('DEBUG_MODE') === 'true' || (isset($_ENV['DEBUG_MODE']) && $_ENV['DEBUG_MODE'] === 'true')) {
            error_log('.env file not found at: ' . $path);
        }
        return false;
    }

    // Read .env file
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    if ($lines === false) {
        error_log('Failed to read .env file');
        return false;
    }

    // Parse each line
    foreach ($lines as $line) {
        // Skip comments and empty lines
        if (strpos(trim($line), '#') === 0 || trim($line) === '') {
            continue;
        }

        // Parse KEY=VALUE format
        if (strpos($line, '=') !== false) {
            list($key, $value) = explode('=', $line, 2);

            $key = trim($key);
            $value = trim($value);

            // Remove quotes if present
            $value = trim($value, '"\'');

            // Set environment variable
            putenv("$key=$value");
            $_ENV[$key] = $value;
            $_SERVER[$key] = $value;
        }
    }

    return true;
}

/**
 * Get environment variable with fallback
 *
 * @param string $key Variable name
 * @param mixed $default Default value if not found
 * @return mixed Variable value or default
 */
function env($key, $default = null) {
    // Try getenv first
    $value = getenv($key);
    if ($value !== false) {
        return $value;
    }

    // Try $_ENV
    if (isset($_ENV[$key])) {
        return $_ENV[$key];
    }

    // Try $_SERVER
    if (isset($_SERVER[$key])) {
        return $_SERVER[$key];
    }

    // Return default
    return $default;
}

/**
 * Validate that required environment variables are set
 *
 * @param array $required Array of required variable names
 * @throws Exception if any required variable is missing
 */
function validateEnv($required) {
    $missing = [];

    foreach ($required as $key) {
        if (env($key) === null || env($key) === '') {
            $missing[] = $key;
        }
    }

    if (!empty($missing)) {
        throw new Exception(
            'Fehlende Umgebungsvariablen: ' . implode(', ', $missing) .
            '. Bitte .env-Datei überprüfen.'
        );
    }
}
