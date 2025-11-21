<?php
/**
 * Anamnesebogen & Zoom Booking API Handler
 *
 * Handles form submission, creates Zoom meetings, and sends confirmation emails
 *
 * @author Wohlfuehlgesundheit - Holistische Darmtherapie
 * @version 2.0 - Security Hardened
 */

// ============================================================================
// LOAD DEPENDENCIES
// ============================================================================

// Load Composer autoloader (try multiple paths)
$autoloadPaths = [
    __DIR__ . '/../../vendor/autoload.php',  // Project root
    __DIR__ . '/../vendor/autoload.php',     // public/vendor
    __DIR__ . '/vendor/autoload.php',        // api/vendor
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
    error_log('Composer autoload not found. Paths tried: ' . implode(', ', $autoloadPaths));
    echo json_encode([
        'success' => false,
        'message' => 'Server-Konfigurationsfehler: Composer autoload nicht gefunden.'
    ], JSON_UNESCAPED_UNICODE);
    exit();
}

require_once __DIR__ . '/env-loader.php';
require_once __DIR__ . '/security.php';
require_once __DIR__ . '/phpmailer-helper.php';

// ============================================================================
// LOAD ENVIRONMENT VARIABLES
// ============================================================================

// Load .env file (auto-detects path)
if (!loadEnv()) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Server-Konfigurationsfehler. Bitte kontaktiere den Administrator.'
    ], JSON_UNESCAPED_UNICODE);
    exit();
}

// Validate required environment variables
try {
    validateEnv([
        'ZOOM_ACCOUNT_ID',
        'ZOOM_CLIENT_ID',
        'ZOOM_CLIENT_SECRET',
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
    error_log('Environment validation failed: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Server-Konfigurationsfehler. Bitte kontaktiere den Administrator.'
    ], JSON_UNESCAPED_UNICODE);
    exit();
}

// ============================================================================
// SECURITY & HEADERS
// ============================================================================

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
    sendJsonResponse(false, 'Nur POST-Anfragen sind erlaubt.');
    exit();
}

// Check rate limiting
checkRateLimit();

// Validate CSRF token
$csrfToken = $_POST['csrf_token'] ?? '';
if (!validateCSRFToken($csrfToken)) {
    sendJsonResponse(false, 'Sicherheitsvalidierung fehlgeschlagen. Bitte lade die Seite neu und versuche es erneut.');
    exit();
}

// ============================================================================
// MAIN PROCESSING
// ============================================================================

try {
    // 1. Validate and sanitize input
    $formData = validateAndSanitizeInput();

    // 2. Get Zoom Access Token
    $accessToken = getZoomAccessToken();

    // 3. Create Zoom Meeting
    $meetingData = createZoomMeeting($accessToken, $formData);

    // 4. Send confirmation email to user
    sendUserConfirmationEmail($formData, $meetingData);

    // 5. Send notification email to admin
    sendAdminNotificationEmail($formData, $meetingData);

    // 6. Success response
    sendJsonResponse(
        true,
        'Vielen Dank! Dein Termin wurde erfolgreich gebucht. Du erhältst in Kürze eine Bestätigungsmail mit den Zoom-Zugangsdaten.'
    );

} catch (Exception $e) {
    // Log error for debugging
    error_log('Anamnese Booking Error: ' . $e->getMessage());

    // Send user-friendly error message
    $debugMode = env('DEBUG_MODE', 'false') === 'true';
    $message = $debugMode
        ? 'Es ist ein Fehler aufgetreten: ' . $e->getMessage()
        : 'Es ist ein Fehler aufgetreten. Bitte versuche es später erneut oder kontaktiere uns direkt.';

    sendJsonResponse(false, $message);
}

// ============================================================================
// VALIDATION & SANITIZATION
// ============================================================================

/**
 * Validate and sanitize all form inputs
 *
 * @return array Sanitized form data
 * @throws Exception if validation fails
 */
function validateAndSanitizeInput() {
    $data = [];

    // ========================================================================
    // REQUIRED FIELDS
    // ========================================================================

    $requiredFields = [
        'vorname' => 'Vorname',
        'nachname' => 'Nachname',
        'email' => 'E-Mail-Adresse',
        'telefon' => 'Telefonnummer',
        'hauptbeschwerde' => 'Hauptbeschwerde',
        'datum' => 'Datum',
        'uhrzeit' => 'Uhrzeit'
    ];

    // Check required fields exist
    foreach ($requiredFields as $field => $label) {
        if (empty($_POST[$field])) {
            throw new Exception("Pflichtfeld fehlt: $label");
        }
    }

    // ========================================================================
    // VALIDATE & SANITIZE REQUIRED FIELDS
    // ========================================================================

    // Name fields (text, max 100 chars)
    $data['vorname'] = validateText($_POST['vorname'], 100);
    $data['nachname'] = validateText($_POST['nachname'], 100);

    if ($data['vorname'] === false || $data['nachname'] === false) {
        throw new Exception('Vor- und Nachname dürfen maximal 100 Zeichen lang sein.');
    }

    // Email
    $data['email'] = validateEmail($_POST['email']);
    if ($data['email'] === false) {
        throw new Exception('Ungültige E-Mail-Adresse.');
    }

    // Phone
    $data['telefon'] = validatePhone($_POST['telefon']);
    if ($data['telefon'] === false) {
        throw new Exception('Ungültige Telefonnummer. Bitte verwende ein gültiges Format.');
    }

    // Hauptbeschwerde (text, max 2000 chars)
    $data['hauptbeschwerde'] = validateText($_POST['hauptbeschwerde'], 2000);
    if ($data['hauptbeschwerde'] === false) {
        throw new Exception('Hauptbeschwerde darf maximal 2000 Zeichen lang sein.');
    }

    // Date
    $data['datum'] = validateDate($_POST['datum'], true);
    if ($data['datum'] === false) {
        throw new Exception('Ungültiges Datum. Das Datum muss in der Zukunft liegen.');
    }

    // Time
    $data['uhrzeit'] = validateTime($_POST['uhrzeit']);
    if ($data['uhrzeit'] === false) {
        throw new Exception('Ungültiges Uhrzeitformat. Bitte verwende HH:MM Format.');
    }

    // ========================================================================
    // OPTIONAL FIELDS - PERSONAL DATA
    // ========================================================================

    $data['adresse'] = !empty($_POST['adresse']) ? validateText($_POST['adresse'], 200) : '';
    $data['plz'] = !empty($_POST['plz']) ? validateText($_POST['plz'], 10) : '';
    $data['ort'] = !empty($_POST['ort']) ? validateText($_POST['ort'], 100) : '';

    // Alter
    $data['alter'] = !empty($_POST['alter']) ? validateAge($_POST['alter']) : null;
    if ($data['alter'] === false) {
        throw new Exception('Ungültiges Alter (0-150 Jahre).');
    }

    // Größe (cm)
    $data['groesse'] = !empty($_POST['groesse']) ? validateNumeric($_POST['groesse'], 50, 250) : null;
    if ($data['groesse'] === false) {
        throw new Exception('Ungültige Größe (50-250 cm).');
    }

    // Gewicht (kg)
    $data['gewicht'] = !empty($_POST['gewicht']) ? validateNumeric($_POST['gewicht'], 20, 300) : null;
    if ($data['gewicht'] === false) {
        throw new Exception('Ungültiges Gewicht (20-300 kg).');
    }

    $data['familienstand'] = !empty($_POST['familienstand']) ? validateText($_POST['familienstand'], 50) : '';
    $data['kinder'] = !empty($_POST['kinder']) ? validateText($_POST['kinder'], 50) : '';
    $data['beruf'] = !empty($_POST['beruf']) ? validateText($_POST['beruf'], 200) : '';
    $data['aufmerksam_durch'] = !empty($_POST['aufmerksam_durch']) ? validateText($_POST['aufmerksam_durch'], 200) : '';
    $data['erwartungen'] = !empty($_POST['erwartungen']) ? validateText($_POST['erwartungen'], 2000) : '';

    // ========================================================================
    // OPTIONAL FIELDS - HEALTH INFORMATION
    // ========================================================================

    $healthFields = [
        'gesundheitsprobleme', 'allergien', 'nahrungsmittelunvertraeglichkeiten',
        'vorerkrankungen', 'medikamente', 'nahrungsergaenzungsmittel'
    ];

    foreach ($healthFields as $field) {
        $data[$field] = !empty($_POST[$field]) ? validateText($_POST[$field], 2000) : '';
    }

    // ========================================================================
    // OPTIONAL FIELDS - NUTRITION & LIFESTYLE
    // ========================================================================

    $nutritionFields = [
        'ernaehrung', 'ernaehrung_details', 'mahlzeiten_pro_tag', 'fruehstueck',
        'mittag', 'abend', 'zwischenmahlzeiten', 'trinkmenge', 'getraenke',
        'alkohol', 'rauchen', 'sport', 'schlaf', 'stress'
    ];

    foreach ($nutritionFields as $field) {
        $data[$field] = !empty($_POST[$field]) ? validateText($_POST[$field], 1000) : '';
    }

    // ========================================================================
    // OPTIONAL FIELDS - DIGESTION
    // ========================================================================

    $digestionFields = [
        'verdauung', 'stuhlgang_haeufigkeit', 'stuhlgang_schmerzen',
        'stuhlgang_auffaelligkeiten', 'stuhlgang_konsistenz',
        'stuhlgang_geruch_saeuerlich', 'winde_geruch'
    ];

    foreach ($digestionFields as $field) {
        $data[$field] = !empty($_POST[$field]) ? validateText($_POST[$field], 1000) : '';
    }

    // ========================================================================
    // OPTIONAL FIELDS - READINESS
    // ========================================================================

    $readinessFields = [
        'bereitschaft_nahrungsergaenzung', 'bereitschaft_investieren',
        'bereitschaft_lebensstil'
    ];

    foreach ($readinessFields as $field) {
        $data[$field] = !empty($_POST[$field]) ? validateText($_POST[$field], 500) : '';
    }

    // Anmerkungen
    $data['anmerkungen'] = !empty($_POST['anmerkungen']) ? validateText($_POST['anmerkungen'], 3000) : '';

    // ========================================================================
    // MEETING DURATION
    // ========================================================================

    $data['dauer'] = intval($_POST['dauer'] ?? 60);
    if (!in_array($data['dauer'], [30, 60])) {
        $data['dauer'] = 60; // Default to 60 minutes
    }

    // ========================================================================
    // PRIVACY POLICY ACCEPTANCE
    // ========================================================================

    if (empty($_POST['datenschutz'])) {
        throw new Exception('Bitte akzeptiere die Datenschutzerklärung.');
    }

    return $data;
}

// ============================================================================
// ZOOM API INTEGRATION
// ============================================================================

/**
 * Get Zoom Access Token using Server-to-Server OAuth
 *
 * @return string Access token
 * @throws Exception if authentication fails
 */
function getZoomAccessToken() {
    $url = 'https://zoom.us/oauth/token';

    $accountId = env('ZOOM_ACCOUNT_ID');
    $clientId = env('ZOOM_CLIENT_ID');
    $clientSecret = env('ZOOM_CLIENT_SECRET');

    $auth = base64_encode($clientId . ':' . $clientSecret);

    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $url . '?grant_type=account_credentials&account_id=' . $accountId,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_HTTPHEADER => [
            'Authorization: Basic ' . $auth,
            'Content-Type: application/x-www-form-urlencoded'
        ],
        CURLOPT_TIMEOUT => 30
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if (curl_errno($ch)) {
        $error = curl_error($ch);
        curl_close($ch);
        throw new Exception('Zoom API Verbindungsfehler: ' . $error);
    }

    curl_close($ch);

    if ($httpCode !== 200) {
        error_log('Zoom OAuth Error (HTTP ' . $httpCode . '): ' . $response);
        throw new Exception('Fehler bei der Zoom-Authentifizierung.');
    }

    $data = json_decode($response, true);

    if (!isset($data['access_token'])) {
        throw new Exception('Kein Access Token von Zoom erhalten.');
    }

    return $data['access_token'];
}

/**
 * Create a scheduled Zoom meeting
 *
 * @param string $accessToken Zoom access token
 * @param array $formData Form data
 * @return array Meeting data
 * @throws Exception if meeting creation fails
 */
function createZoomMeeting($accessToken, $formData) {
    $url = 'https://api.zoom.us/v2/users/me/meetings';

    // Combine date and time
    $dateTime = $formData['datum'] . ' ' . $formData['uhrzeit'];
    $startTime = date('Y-m-d\TH:i:s', strtotime($dateTime));

    // Meeting topic
    $topic = 'Erstgespräch: ' . $formData['vorname'] . ' ' . $formData['nachname'];

    // Meeting payload
    $meetingData = [
        'topic' => $topic,
        'type' => 2, // Scheduled meeting
        'start_time' => $startTime,
        'duration' => $formData['dauer'],
        'timezone' => 'Europe/Berlin',
        'agenda' => 'Anamnesegespräch - Holistische Darmtherapie',
        'settings' => [
            'host_video' => true,
            'participant_video' => true,
            'join_before_host' => false,
            'mute_upon_entry' => false,
            'watermark' => false,
            'use_pmi' => false,
            'approval_type' => 2, // No registration required
            'audio' => 'both',
            'auto_recording' => 'none',
            'waiting_room' => true,
            'meeting_authentication' => false
        ]
    ];

    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_HTTPHEADER => [
            'Authorization: Bearer ' . $accessToken,
            'Content-Type: application/json'
        ],
        CURLOPT_POSTFIELDS => json_encode($meetingData),
        CURLOPT_TIMEOUT => 30
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if (curl_errno($ch)) {
        $error = curl_error($ch);
        curl_close($ch);
        throw new Exception('Zoom API Verbindungsfehler: ' . $error);
    }

    curl_close($ch);

    if ($httpCode !== 201) {
        error_log('Zoom Meeting Creation Error (HTTP ' . $httpCode . '): ' . $response);
        throw new Exception('Fehler beim Erstellen des Zoom-Meetings.');
    }

    $meeting = json_decode($response, true);

    if (!isset($meeting['id'])) {
        throw new Exception('Keine Meeting-ID von Zoom erhalten.');
    }

    return $meeting;
}

// ============================================================================
// EMAIL FUNCTIONS
// ============================================================================

/**
 * Send confirmation email to user
 *
 * @param array $formData Form data
 * @param array $meetingData Zoom meeting data
 */
function sendUserConfirmationEmail($formData, $meetingData) {
    $to = $formData['email'];
    $subject = 'Terminbestätigung - Dein Zoom-Erstgespräch';

    $fromEmail = env('FROM_EMAIL');
    $fromName = env('FROM_NAME');
    $adminEmail = env('ADMIN_EMAIL');

    // Format date and time in German
    $datumFormatiert = date('d.m.Y', strtotime($formData['datum']));
    $startTime = date('H:i', strtotime($meetingData['start_time']));

    // HTML email body
    $message = "
    <!DOCTYPE html>
    <html lang='de'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background-color: #2a700d; color: white; padding: 20px; text-align: center; border-radius: 8px 8px 0 0; }
            .content { background-color: #f9fafb; padding: 30px; border-radius: 0 0 8px 8px; }
            .meeting-details { background-color: white; padding: 20px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #2a700d; }
            .button { display: inline-block; background-color: #2a700d; color: white; padding: 12px 30px; text-decoration: none; border-radius: 6px; margin: 10px 0; }
            .footer { text-align: center; margin-top: 30px; font-size: 12px; color: #666; }
            strong { color: #2a700d; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h1>Terminbestätigung</h1>
            </div>
            <div class='content'>
                <p>Hallo {$formData['vorname']},</p>

                <p>vielen Dank für dein Vertrauen! Dein Zoom-Erstgespräch wurde erfolgreich gebucht.</p>

                <div class='meeting-details'>
                    <h2 style='margin-top: 0; color: #2a700d;'>Deine Termin-Details</h2>
                    <p><strong>Datum:</strong> {$datumFormatiert}</p>
                    <p><strong>Uhrzeit:</strong> {$startTime} Uhr</p>
                    <p><strong>Dauer:</strong> {$formData['dauer']} Minuten</p>
                    <p><strong>Meeting-ID:</strong> {$meetingData['id']}</p>
                    <p><strong>Passcode:</strong> {$meetingData['password']}</p>
                </div>

                <p><strong>So nimmst du am Meeting teil:</strong></p>
                <p>Klick zum gewählten Zeitpunkt einfach auf folgenden Link:</p>

                <p style='text-align: center;'>
                    <a href='{$meetingData['join_url']}' class='button'>Zum Zoom-Meeting</a>
                </p>

                <p style='font-size: 14px; color: #666;'>
                    Alternativ kannst du auch die Zoom-App öffnen und die Meeting-ID manuell eingeben.
                </p>

                <p><strong>Wichtige Hinweise:</strong></p>
                <ul>
                    <li>Bitte stell sicher, dass du Zoom installiert hast oder nutze die Browser-Version</li>
                    <li>Teste vorab deine Kamera und dein Mikrofon</li>
                    <li>Such dir einen ruhigen Ort für das Gespräch</li>
                </ul>

                <p>Ich freue mich auf das Gespräch mit dir!</p>

                <p>Herzliche Grüße,<br>
                Stefanie von Wohlfühlgesundheit</p>

                <div class='footer'>
                    <p>Bei Fragen oder Änderungswünschen kontaktiere mich bitte unter:<br>
                    <a href='mailto:{$adminEmail}'>{$adminEmail}</a></p>
                </div>
            </div>
        </div>
    </body>
    </html>
    ";

    // Send email via PHPMailer (IONOS SMTP)
    $success = sendHtmlEmail($to, $subject, $message, null, $adminEmail);

    if (!$success) {
        error_log('Failed to send user confirmation email to: ' . $to);
    }
}

/**
 * Send notification email to admin
 *
 * @param array $formData Form data
 * @param array $meetingData Zoom meeting data
 */
function sendAdminNotificationEmail($formData, $meetingData) {
    $adminEmail = env('ADMIN_EMAIL');
    $fromEmail = env('FROM_EMAIL');
    $fromName = env('FROM_NAME');

    $subject = 'Neue Anamnese & Terminbuchung: ' . $formData['vorname'] . ' ' . $formData['nachname'];

    // Format date and time
    $datumFormatiert = date('d.m.Y', strtotime($formData['datum']));
    $startTime = date('H:i', strtotime($meetingData['start_time']));

    // Build comprehensive admin message
    $message = "
Neue Anamnese und Terminbuchung
================================

PERSÖNLICHE DATEN:
------------------
Name: {$formData['vorname']} {$formData['nachname']}
E-Mail: {$formData['email']}
Telefon: {$formData['telefon']}
Adresse: {$formData['adresse']}, {$formData['plz']} {$formData['ort']}

Alter: {$formData['alter']}
Größe: {$formData['groesse']} cm
Gewicht: {$formData['gewicht']} kg
Familienstand: {$formData['familienstand']}
Kinder im Haushalt: {$formData['kinder']}
Beruf: {$formData['beruf']}

Aufmerksam geworden durch: {$formData['aufmerksam_durch']}
Erwartungen: {$formData['erwartungen']}

GESUNDHEITSINFORMATIONEN:
-------------------------
Hauptbeschwerde:
{$formData['hauptbeschwerde']}

Weitere gesundheitliche Probleme:
{$formData['gesundheitsprobleme']}

Allergien:
{$formData['allergien']}

Nahrungsmittelunverträglichkeiten:
{$formData['nahrungsmittelunvertraeglichkeiten']}

Vorerkrankungen:
{$formData['vorerkrankungen']}

Aktuelle Medikamente:
{$formData['medikamente']}

Nahrungsergänzungsmittel:
{$formData['nahrungsergaenzungsmittel']}

ERNÄHRUNG & LEBENSSTIL:
-----------------------
Ernährungsform: {$formData['ernaehrung']}
Details: {$formData['ernaehrung_details']}

Mahlzeiten pro Tag: {$formData['mahlzeiten_pro_tag']}
Frühstück: {$formData['fruehstueck']}
Mittagessen: {$formData['mittag']}
Abendessen: {$formData['abend']}
Zwischenmahlzeiten: {$formData['zwischenmahlzeiten']}

Trinkmenge: {$formData['trinkmenge']}
Getränke: {$formData['getraenke']}
Alkoholkonsum: {$formData['alkohol']}
Rauchen: {$formData['rauchen']}

Sport/Bewegung: {$formData['sport']}
Schlaf: {$formData['schlaf']}
Stress: {$formData['stress']}

VERDAUUNG & STUHLGANG:
----------------------
Verdauung allgemein: {$formData['verdauung']}
Stuhlgang-Häufigkeit: {$formData['stuhlgang_haeufigkeit']}
Schmerzen beim Stuhlgang: {$formData['stuhlgang_schmerzen']}
Auffälligkeiten: {$formData['stuhlgang_auffaelligkeiten']}
Konsistenz: {$formData['stuhlgang_konsistenz']}
Säuerlicher Geruch: {$formData['stuhlgang_geruch_saeuerlich']}
Winde riechen nach faulen Eiern: {$formData['winde_geruch']}

BEREITSCHAFT:
-------------
Nahrungsergänzungsmittel einnehmen: {$formData['bereitschaft_nahrungsergaenzung']}
In sich investieren: {$formData['bereitschaft_investieren']}
Lebensstil anpassen: {$formData['bereitschaft_lebensstil']}

WEITERE ANMERKUNGEN:
--------------------
{$formData['anmerkungen']}

TERMIN-DETAILS:
---------------
Datum: {$datumFormatiert}
Uhrzeit: {$startTime} Uhr
Dauer: {$formData['dauer']} Minuten

ZOOM-MEETING:
-------------
Meeting-ID: {$meetingData['id']}
Passcode: {$meetingData['password']}

Als Host teilnehmen:
{$meetingData['start_url']}

Meeting-Link für Teilnehmer:
{$meetingData['join_url']}

================================
Automatische Benachrichtigung vom Anamnese-System
    ";

    // Send email via PHPMailer (IONOS SMTP)
    $success = sendTextEmail($adminEmail, $subject, $message, $formData['email']);

    if (!$success) {
        error_log('Failed to send admin notification email');
    }
}

// ============================================================================
// UTILITY FUNCTIONS
// ============================================================================

/**
 * Send JSON response and exit
 *
 * @param bool $success Success status
 * @param string $message Message to user
 */
function sendJsonResponse($success, $message) {
    echo json_encode([
        'success' => $success,
        'message' => $message
    ], JSON_UNESCAPED_UNICODE);
    exit();
}
