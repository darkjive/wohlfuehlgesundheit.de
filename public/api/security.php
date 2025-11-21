<?php
/**
 * Security Functions
 *
 * Rate-Limiting, CSRF-Protection, CORS, Input-Validation
 *
 * @author Wohlfuehlgesundheit - Holistische Darmtherapie
 * @version 1.0
 */

// ============================================================================
// CORS PROTECTION
// ============================================================================

/**
 * Check and set CORS headers based on allowed origins
 *
 * @return bool True if origin is allowed, false otherwise
 */
function checkCORS() {
    // Get allowed origins from environment
    $allowedOriginsStr = env('ALLOWED_ORIGINS', '');
    $allowedOrigins = array_map('trim', explode(',', $allowedOriginsStr));

    // Get request origin
    $origin = $_SERVER['HTTP_ORIGIN'] ?? '';

    // Check if origin is allowed
    if (in_array($origin, $allowedOrigins)) {
        header('Access-Control-Allow-Origin: ' . $origin);
        header('Access-Control-Allow-Methods: POST, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, X-CSRF-Token');
        header('Access-Control-Allow-Credentials: true');
        return true;
    }

    // For same-origin requests (no Origin header), allow
    if (empty($origin)) {
        return true;
    }

    // Origin not allowed
    http_response_code(403);
    echo json_encode([
        'success' => false,
        'message' => 'Zugriff von dieser Domain nicht erlaubt.'
    ], JSON_UNESCAPED_UNICODE);
    exit();
}

// ============================================================================
// RATE LIMITING (File-based)
// ============================================================================

/**
 * Check rate limit for IP address
 * Uses file-based storage (no database required)
 *
 * @param int $maxRequests Maximum requests allowed
 * @param int $timeWindow Time window in seconds
 * @return bool True if within limit, false if exceeded
 */
function checkRateLimit($maxRequests = null, $timeWindow = null) {
    // Get settings from environment
    $maxRequests = $maxRequests ?? (int)env('RATE_LIMIT_MAX_REQUESTS', 5);
    $timeWindow = $timeWindow ?? (int)env('RATE_LIMIT_TIME_WINDOW', 3600);

    // Get client IP
    $ip = getClientIP();

    // Create rate limit directory if not exists
    $rateLimitDir = __DIR__ . '/../_rate_limit';
    if (!is_dir($rateLimitDir)) {
        mkdir($rateLimitDir, 0755, true);
    }

    // File for this IP
    $filename = $rateLimitDir . '/' . md5($ip) . '.json';

    // Get current requests
    $requests = [];
    if (file_exists($filename)) {
        $content = file_get_contents($filename);
        $requests = json_decode($content, true) ?: [];
    }

    // Remove old requests (outside time window)
    $currentTime = time();
    $requests = array_filter($requests, function($timestamp) use ($currentTime, $timeWindow) {
        return ($currentTime - $timestamp) < $timeWindow;
    });

    // Check if limit exceeded
    if (count($requests) >= $maxRequests) {
        $oldestRequest = min($requests);
        $waitTime = $timeWindow - ($currentTime - $oldestRequest);

        http_response_code(429);
        echo json_encode([
            'success' => false,
            'message' => "Zu viele Anfragen. Bitte versuche es in " . ceil($waitTime / 60) . " Minute(n) erneut.",
            'retry_after' => $waitTime
        ], JSON_UNESCAPED_UNICODE);
        exit();
    }

    // Add current request
    $requests[] = $currentTime;

    // Save to file
    file_put_contents($filename, json_encode($requests));

    return true;
}

/**
 * Get real client IP address
 * Considers proxy headers
 *
 * @return string Client IP address
 */
function getClientIP() {
    $headers = [
        'HTTP_CF_CONNECTING_IP', // Cloudflare
        'HTTP_X_FORWARDED_FOR',  // Proxy
        'HTTP_X_REAL_IP',        // Nginx
        'REMOTE_ADDR'            // Direct connection
    ];

    foreach ($headers as $header) {
        if (!empty($_SERVER[$header])) {
            $ip = $_SERVER[$header];
            // For X-Forwarded-For, take first IP
            if (strpos($ip, ',') !== false) {
                $ip = trim(explode(',', $ip)[0]);
            }
            // Validate IP
            if (filter_var($ip, FILTER_VALIDATE_IP)) {
                return $ip;
            }
        }
    }

    return '0.0.0.0';
}

/**
 * Clean up old rate limit files (maintenance)
 * Call this periodically (e.g., via cron)
 *
 * @param int $maxAge Maximum age in seconds (default: 24 hours)
 */
function cleanupRateLimitFiles($maxAge = 86400) {
    $rateLimitDir = __DIR__ . '/../_rate_limit';

    if (!is_dir($rateLimitDir)) {
        return;
    }

    $files = glob($rateLimitDir . '/*.json');
    $currentTime = time();

    foreach ($files as $file) {
        if (($currentTime - filemtime($file)) > $maxAge) {
            unlink($file);
        }
    }
}

// ============================================================================
// CSRF PROTECTION
// ============================================================================

/**
 * Generate CSRF token
 * Uses session-less approach with HMAC
 *
 * @return string CSRF token
 */
function generateCSRFToken() {
    $secret = env('CSRF_SECRET', 'change_this_secret');
    $timestamp = time();
    $ip = getClientIP();

    // Token format: timestamp|hmac
    $data = $timestamp . '|' . $ip;
    $hmac = hash_hmac('sha256', $data, $secret);

    return base64_encode($timestamp . '|' . $hmac);
}

/**
 * Validate CSRF token
 *
 * @param string $token Token to validate
 * @param int $maxAge Maximum token age in seconds (default: 1 hour)
 * @return bool True if valid, false otherwise
 */
function validateCSRFToken($token, $maxAge = 3600) {
    if (empty($token)) {
        return false;
    }

    $secret = env('CSRF_SECRET', 'change_this_secret');

    // Decode token
    $decoded = base64_decode($token);
    if ($decoded === false) {
        return false;
    }

    $parts = explode('|', $decoded);
    if (count($parts) !== 2) {
        return false;
    }

    list($timestamp, $hmac) = $parts;

    // Check token age
    if ((time() - $timestamp) > $maxAge) {
        return false;
    }

    // Verify HMAC
    $ip = getClientIP();
    $data = $timestamp . '|' . $ip;
    $expectedHmac = hash_hmac('sha256', $data, $secret);

    return hash_equals($expectedHmac, $hmac);
}

// ============================================================================
// INPUT VALIDATION
// ============================================================================

/**
 * Validate email address
 *
 * @param string $email Email to validate
 * @return string|false Sanitized email or false if invalid
 */
function validateEmail($email) {
    $email = filter_var($email, FILTER_SANITIZE_EMAIL);

    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return $email;
    }

    return false;
}

/**
 * Validate phone number (German format)
 *
 * @param string $phone Phone number to validate
 * @return string|false Sanitized phone or false if invalid
 */
function validatePhone($phone) {
    // Remove all non-digits except +, -, (, ), spaces
    $cleaned = preg_replace('/[^0-9+\-\(\) ]/', '', $phone);

    // Check length (6-20 characters)
    if (strlen($cleaned) >= 6 && strlen($cleaned) <= 20) {
        return $cleaned;
    }

    return false;
}

/**
 * Validate age
 *
 * @param mixed $age Age to validate
 * @return int|false Age as integer or false if invalid
 */
function validateAge($age) {
    if (!is_numeric($age)) {
        return false;
    }

    $age = (int)$age;

    if ($age >= 0 && $age <= 150) {
        return $age;
    }

    return false;
}

/**
 * Validate numeric value with range
 *
 * @param mixed $value Value to validate
 * @param int $min Minimum value
 * @param int $max Maximum value
 * @return int|float|false Value or false if invalid
 */
function validateNumeric($value, $min = null, $max = null) {
    if (!is_numeric($value)) {
        return false;
    }

    $value = is_float($value) ? (float)$value : (int)$value;

    if ($min !== null && $value < $min) {
        return false;
    }

    if ($max !== null && $value > $max) {
        return false;
    }

    return $value;
}

/**
 * Validate and sanitize text with length limit
 *
 * @param string $text Text to validate
 * @param int $maxLength Maximum length
 * @return string|false Sanitized text or false if too long
 */
function validateText($text, $maxLength = 5000) {
    $text = trim($text);

    if (strlen($text) > $maxLength) {
        return false;
    }

    return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}

/**
 * Validate date (must be in future)
 *
 * @param string $date Date string (Y-m-d format)
 * @param bool $futureOnly Only allow future dates
 * @return string|false Valid date string or false
 */
function validateDate($date, $futureOnly = true) {
    $timestamp = strtotime($date);

    if ($timestamp === false) {
        return false;
    }

    if ($futureOnly) {
        $tomorrow = strtotime('tomorrow');
        if ($timestamp < $tomorrow) {
            return false;
        }
    }

    return date('Y-m-d', $timestamp);
}

/**
 * Validate time format (HH:MM)
 *
 * @param string $time Time string
 * @return string|false Valid time string or false
 */
function validateTime($time) {
    if (preg_match('/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/', $time)) {
        return $time;
    }

    return false;
}
