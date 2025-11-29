<?php
/**
 * Instagram Feed API Endpoint
 *
 * Fetches Instagram posts using EnsembleData API
 * Implements caching to minimize API calls
 *
 * @author Wohlfuehlgesundheit - Holistische Darmtherapie
 * @version 1.0
 */

// ============================================================================
// BOOTSTRAP
// ============================================================================

require_once __DIR__ . '/bootstrap.php';

// ============================================================================
// CONFIGURATION
// ============================================================================

// Cache settings
define('INSTAGRAM_CACHE_FILE', __DIR__ . '/cache/instagram-feed.json');
define('INSTAGRAM_CACHE_DURATION', 3600); // 1 hour in seconds

// Rate limiting for Instagram endpoint
define('RATE_LIMIT_INSTAGRAM_MAX_REQUESTS', 10);
define('RATE_LIMIT_INSTAGRAM_TIME_WINDOW', 3600); // 1 hour

// ============================================================================
// SECURITY
// ============================================================================

// Set JSON header
header('Content-Type: application/json; charset=utf-8');

// CORS headers
setCorsHeaders();

// Handle preflight
handlePreflightRequest();

// Rate limiting
checkRateLimit(RATE_LIMIT_INSTAGRAM_MAX_REQUESTS, RATE_LIMIT_INSTAGRAM_TIME_WINDOW);

// ============================================================================
// HELPER FUNCTIONS
// ============================================================================

/**
 * Get cached Instagram feed if valid
 *
 * @return array|null Cached data or null if cache is invalid
 */
function getCachedFeed() {
    if (!file_exists(INSTAGRAM_CACHE_FILE)) {
        return null;
    }

    $cacheAge = time() - filemtime(INSTAGRAM_CACHE_FILE);
    if ($cacheAge > INSTAGRAM_CACHE_DURATION) {
        return null;
    }

    $cachedData = file_get_contents(INSTAGRAM_CACHE_FILE);
    return json_decode($cachedData, true);
}

/**
 * Save Instagram feed to cache
 *
 * @param array $data Data to cache
 * @return bool Success status
 */
function saveCachedFeed($data) {
    $cacheDir = dirname(INSTAGRAM_CACHE_FILE);
    if (!is_dir($cacheDir)) {
        mkdir($cacheDir, 0755, true);
    }

    return file_put_contents(
        INSTAGRAM_CACHE_FILE,
        json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT)
    ) !== false;
}

/**
 * Fetch Instagram posts from EnsembleData API
 *
 * @param string $username Instagram username
 * @param string $apiToken EnsembleData API token
 * @return array API response
 */
function fetchInstagramPosts($username, $apiToken) {
    $url = 'https://ensembledata.com/apis/instagram/user/posts';

    $postData = [
        'username' => $username,
        'count' => 12 // Fetch latest 12 posts
    ];

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode($postData),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            'Authorization: Bearer ' . $apiToken,
            'Content-Type: application/json'
        ],
        CURLOPT_TIMEOUT => 30,
        CURLOPT_SSL_VERIFYPEER => true,
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);

    if ($error) {
        throw new Exception('cURL Error: ' . $error);
    }

    if ($httpCode !== 200) {
        throw new Exception('EnsembleData API returned status code: ' . $httpCode);
    }

    $data = json_decode($response, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('Invalid JSON response from EnsembleData API');
    }

    return $data;
}

// ============================================================================
// MAIN LOGIC
// ============================================================================

try {
    // Check for required environment variables
    $apiToken = getenv('ENSEMBLEDATA_API_TOKEN');
    $username = getenv('INSTAGRAM_USERNAME');

    if (!$apiToken || !$username) {
        throw new Exception('Instagram API not configured. Please set ENSEMBLEDATA_API_TOKEN and INSTAGRAM_USERNAME in .env');
    }

    // Try to get cached feed first
    $cachedFeed = getCachedFeed();

    if ($cachedFeed !== null) {
        // Return cached data
        echo json_encode([
            'success' => true,
            'data' => $cachedFeed,
            'cached' => true,
            'cache_age' => time() - filemtime(INSTAGRAM_CACHE_FILE)
        ], JSON_UNESCAPED_UNICODE);
        exit();
    }

    // Fetch fresh data from API
    $apiResponse = fetchInstagramPosts($username, $apiToken);

    // Check if API response is valid
    if (!isset($apiResponse['data']) || !is_array($apiResponse['data'])) {
        throw new Exception('Invalid response format from EnsembleData API');
    }

    // Transform data for frontend
    $posts = [];
    foreach ($apiResponse['data'] as $post) {
        $posts[] = [
            'id' => $post['id'] ?? '',
            'caption' => $post['caption'] ?? '',
            'media_url' => $post['display_url'] ?? $post['thumbnail_url'] ?? '',
            'permalink' => $post['shortcode'] ? 'https://www.instagram.com/p/' . $post['shortcode'] : '',
            'timestamp' => $post['taken_at_timestamp'] ?? time(),
            'type' => $post['is_video'] ? 'video' : 'image',
            'likes' => $post['edge_liked_by']['count'] ?? 0,
            'comments' => $post['edge_media_to_comment']['count'] ?? 0
        ];
    }

    // Save to cache
    saveCachedFeed($posts);

    // Return response
    echo json_encode([
        'success' => true,
        'data' => $posts,
        'cached' => false
    ], JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    http_response_code(500);

    $debugMode = getenv('DEBUG_MODE') === 'true';

    echo json_encode([
        'success' => false,
        'message' => 'Fehler beim Abrufen des Instagram-Feeds.',
        'error' => $debugMode ? $e->getMessage() : null
    ], JSON_UNESCAPED_UNICODE);

    if ($debugMode) {
        error_log('Instagram Feed Error: ' . $e->getMessage());
    }
}
