<?php
/**
 * PHPMailer Helper für IONOS SMTP
 *
 * Stellt konfigurierte PHPMailer-Instanzen bereit
 * Dokumentation: https://www.ionos.de/digitalguide/e-mail/e-mail-technik/phpmailer/
 */

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

/**
 * Erstellt und konfiguriert eine PHPMailer-Instanz für IONOS SMTP
 *
 * @return PHPMailer Konfigurierte PHPMailer-Instanz
 * @throws Exception Bei Konfigurationsfehlern
 */
function createMailer() {
    $mail = new PHPMailer(true);

    try {
        // Server-Einstellungen
        $mail->isSMTP();
        $mail->Host = env('SMTP_HOST', 'smtp.ionos.de');
        $mail->SMTPAuth = true;
        $mail->Username = env('SMTP_USERNAME');
        $mail->Password = env('SMTP_PASSWORD');
        $mail->SMTPSecure = env('SMTP_ENCRYPTION', 'tls');
        $mail->Port = (int)env('SMTP_PORT', 587);
        $mail->CharSet = 'UTF-8';

        // Absender setzen
        $fromEmail = env('FROM_EMAIL');
        $fromName = env('FROM_NAME', 'Wohlfühlgesundheit');
        $mail->setFrom($fromEmail, $fromName);

        // Debug-Modus (nur wenn DEBUG_MODE aktiviert)
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
 * Sendet eine einfache Text-E-Mail über IONOS SMTP
 *
 * @param string $to Empfänger-E-Mail
 * @param string $subject Betreff
 * @param string $message Nachricht (plain text)
 * @param string|null $replyTo Optional: Reply-To-Adresse
 * @return bool True bei Erfolg, False bei Fehler
 */
function sendTextEmail($to, $subject, $message, $replyTo = null) {
    try {
        $mail = createMailer();

        // Empfänger
        $mail->addAddress($to);

        // Reply-To (optional)
        if ($replyTo) {
            $mail->addReplyTo($replyTo);
        }

        // Inhalt
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
 * Sendet eine HTML-E-Mail über IONOS SMTP
 *
 * @param string $to Empfänger-E-Mail
 * @param string $subject Betreff
 * @param string $htmlMessage HTML-Nachricht
 * @param string|null $textMessage Optional: Plain-Text-Alternative
 * @param string|null $replyTo Optional: Reply-To-Adresse
 * @return bool True bei Erfolg, False bei Fehler
 */
function sendHtmlEmail($to, $subject, $htmlMessage, $textMessage = null, $replyTo = null) {
    try {
        $mail = createMailer();

        // Empfänger
        $mail->addAddress($to);

        // Reply-To (optional)
        if ($replyTo) {
            $mail->addReplyTo($replyTo);
        }

        // Inhalt
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
