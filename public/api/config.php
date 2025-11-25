<?php
/**
 * Configuration Constants
 *
 * Centralized configuration for API endpoints
 * Eliminates magic numbers throughout the codebase
 *
 * @author Wohlfuehlgesundheit - Holistische Darmtherapie
 * @version 1.0
 */

// ============================================================================
// RATE LIMITING
// ============================================================================

// Contact Form Rate Limits
define('RATE_LIMIT_CONTACT_FORM_MAX_REQUESTS', 5);
define('RATE_LIMIT_CONTACT_FORM_TIME_WINDOW', 3600); // 1 hour

// Anamnese/Booking Form Rate Limits
define('RATE_LIMIT_ANAMNESE_MAX_REQUESTS', 5);
define('RATE_LIMIT_ANAMNESE_TIME_WINDOW', 3600); // 1 hour

// CSRF Token Rate Limits
define('RATE_LIMIT_CSRF_MAX_REQUESTS', 20);
define('RATE_LIMIT_CSRF_TIME_WINDOW', 3600); // 1 hour

// Default Rate Limits (for checkRateLimit without params)
define('RATE_LIMIT_DEFAULT_MAX_REQUESTS', 5);
define('RATE_LIMIT_DEFAULT_TIME_WINDOW', 3600); // 1 hour

// Rate Limit Cleanup
define('RATE_LIMIT_CLEANUP_MAX_AGE', 86400); // 24 hours
define('RATE_LIMIT_CLEANUP_PROBABILITY', 1); // 1% chance per request

// ============================================================================
// CSRF PROTECTION
// ============================================================================

define('CSRF_TOKEN_MAX_AGE', 1800); // 30 minutes

// ============================================================================
// VALIDATION LIMITS
// ============================================================================

// Text Fields
define('VALIDATION_NAME_MAX_LENGTH', 100);
define('VALIDATION_EMAIL_MAX_LENGTH', 255);
define('VALIDATION_PHONE_MIN_LENGTH', 6);
define('VALIDATION_PHONE_MAX_LENGTH', 20);
define('VALIDATION_ADDRESS_MAX_LENGTH', 200);
define('VALIDATION_ZIP_MAX_LENGTH', 10);
define('VALIDATION_CITY_MAX_LENGTH', 100);
define('VALIDATION_JOB_MAX_LENGTH', 200);

// Text Areas
define('VALIDATION_MESSAGE_MAX_LENGTH', 5000);
define('VALIDATION_SHORT_TEXT_MAX_LENGTH', 2000);
define('VALIDATION_MEDIUM_TEXT_MAX_LENGTH', 1000);
define('VALIDATION_LONG_TEXT_MAX_LENGTH', 3000);

// Numeric Values
define('VALIDATION_AGE_MIN', 0);
define('VALIDATION_AGE_MAX', 150);
define('VALIDATION_HEIGHT_MIN', 50);  // cm
define('VALIDATION_HEIGHT_MAX', 250); // cm
define('VALIDATION_WEIGHT_MIN', 20);  // kg
define('VALIDATION_WEIGHT_MAX', 300); // kg

// ============================================================================
// ZOOM MEETING SETTINGS
// ============================================================================

define('ZOOM_MEETING_DURATION_30', 30);
define('ZOOM_MEETING_DURATION_60', 60);
define('ZOOM_MEETING_DEFAULT_DURATION', 60);
define('ZOOM_MEETING_TIMEZONE', 'Europe/Berlin');

// ============================================================================
// EMAIL SETTINGS
// ============================================================================

define('EMAIL_REPLY_TO_TIMEOUT', 24); // Hours to respond
