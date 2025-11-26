<?php
/**
 * PHPMailer Helper for IONOS SMTP
 *
 * Provides configured PHPMailer instances
 * Documentation: https://www.ionos.de/digitalguide/e-mail/e-mail-technik/phpmailer/
 */

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

/**
 * Creates and configures a PHPMailer instance for IONOS SMTP
 *
 * @return PHPMailer Configured PHPMailer instance
 * @throws Exception On configuration errors
 */
function createMailer() {
    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = env('SMTP_HOST', 'smtp.ionos.de');
        $mail->SMTPAuth = true;
        $mail->Username = env('SMTP_USERNAME');
        $mail->Password = env('SMTP_PASSWORD');
        $mail->SMTPSecure = env('SMTP_ENCRYPTION', 'tls');
        $mail->Port = (int)env('SMTP_PORT', 587);
        $mail->CharSet = 'UTF-8';

        // Set sender
        $fromEmail = env('FROM_EMAIL');
        $fromName = env('FROM_NAME', 'Wohlfühlgesundheit');
        $mail->setFrom($fromEmail, $fromName);

        // Debug mode (only when DEBUG_MODE is enabled)
        if (env('DEBUG_MODE') === 'true') {
            $mail->SMTPDebug = 2; // Verbose debug output
            $mail->Debugoutput = 'error_log';
        }

        return $mail;

    } catch (Exception $e) {
        error_log('PHPMailer Configuration Error: ' . $e->getMessage());
        throw $e;
    }
}

/**
 * Sends a simple text email via IONOS SMTP
 *
 * @param string $to Recipient email
 * @param string $subject Subject
 * @param string $message Message (plain text)
 * @param string|null $replyTo Optional: Reply-To address
 * @return bool True on success, False on error
 */
function sendTextEmail($to, $subject, $message, $replyTo = null) {
    try {
        $mail = createMailer();

        // Recipient
        $mail->addAddress($to);

        // Reply-To (optional)
        if ($replyTo) {
            $mail->addReplyTo($replyTo);
        }

        // Content
        $mail->isHTML(false);
        $mail->Subject = $subject;
        $mail->Body = $message;

        return $mail->send();

    } catch (Exception $e) {
        error_log('Email sending failed: ' . $e->getMessage());
        return false;
    }
}

/**
 * Sends an HTML email via IONOS SMTP
 *
 * @param string $to Recipient email
 * @param string $subject Subject
 * @param string $htmlMessage HTML message
 * @param string|null $textMessage Optional: Plain text alternative
 * @param string|null $replyTo Optional: Reply-To address
 * @return bool True on success, False on error
 */
function sendHtmlEmail($to, $subject, $htmlMessage, $textMessage = null, $replyTo = null) {
    try {
        $mail = createMailer();

        // Recipient
        $mail->addAddress($to);

        // Reply-To (optional)
        if ($replyTo) {
            $mail->addReplyTo($replyTo);
        }

        // Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $htmlMessage;

        // Alternative plain-text version
        if ($textMessage) {
            $mail->AltBody = $textMessage;
        }

        return $mail->send();

    } catch (Exception $e) {
        error_log('Email sending failed: ' . $e->getMessage());
        return false;
    }
}
