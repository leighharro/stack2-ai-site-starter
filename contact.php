<?php

declare(strict_types=1);

/**
 * Contact form handler.
 *
 * Uses the official PHPMailer classes shipped under lib/PHPMailer/src/.
 * No Composer and no vendor/ directory.
 */

use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

require __DIR__ . '/lib/PHPMailer/src/Exception.php';
require __DIR__ . '/lib/PHPMailer/src/PHPMailer.php';
require __DIR__ . '/lib/PHPMailer/src/SMTP.php';

function contact_fail(string $message): void
{
    http_response_code(400);
    header('Content-Type: text/html; charset=UTF-8');
    $safe = htmlspecialchars($message, ENT_QUOTES, 'UTF-8');
    echo '<!DOCTYPE html><html lang="en-AU"><head><meta charset="utf-8">';
    echo '<meta name="viewport" content="width=device-width, initial-scale=1">';
    echo '<title>Message not sent</title>';
    echo '<link rel="stylesheet" href="style.css"></head><body class="page">';
    echo '<main class="wrap card-page"><h1>Message not sent</h1>';
    echo '<p>' . $safe . '</p>';
    echo '<p><a class="btn" href="index.html#contact">Back to the form</a></p>';
    echo '</main></body></html>';
    exit;
}

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    header('Location: index.html#contact', true, 303);
    exit;
}

$honeypot = trim((string) ($_POST['website'] ?? ''));
if ($honeypot !== '') {
    header('Location: thank-you.html', true, 303);
    exit;
}

$name = trim((string) ($_POST['name'] ?? ''));
$email = trim((string) ($_POST['email'] ?? ''));
$message = trim((string) ($_POST['message'] ?? ''));
$company = trim((string) ($_POST['company'] ?? ''));

if ($name === '' || $email === '' || $message === '') {
    contact_fail('Please fill in your name, email address, and message.');
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    contact_fail('Please enter a valid email address.');
}

if (strlen($name) > 200 || strlen($email) > 254 || strlen($company) > 200 || strlen($message) > 5000) {
    contact_fail('One of the fields is too long. Please shorten it and try again.');
}

$configPath = __DIR__ . '/config.smtp.php';
if (!is_readable($configPath)) {
    contact_fail('SMTP is not configured yet. Copy config.smtp.php.example to config.smtp.php and add your mailbox details.');
}

$config = require $configPath;
if (!is_array($config)) {
    contact_fail('config.smtp.php must return an array of SMTP settings.');
}

$required = ['host', 'port', 'username', 'password', 'from_email', 'to_email'];
foreach ($required as $key) {
    if (!isset($config[$key]) || $config[$key] === '') {
        contact_fail('config.smtp.php is missing the "' . $key . '" setting.');
    }
}

$encryption = strtolower((string) ($config['encryption'] ?? 'tls'));
$secure = PHPMailer::ENCRYPTION_STARTTLS;
if ($encryption === 'ssl' || $encryption === 'smtps') {
    $secure = PHPMailer::ENCRYPTION_SMTPS;
}

$bodyLines = [
    'New enquiry from the Example Studio website.',
    '',
    'Name: ' . $name,
    'Email: ' . $email,
];
if ($company !== '') {
    $bodyLines[] = 'Company: ' . $company;
}
$bodyLines[] = '';
$bodyLines[] = 'Message:';
$bodyLines[] = $message;

try {
    $mail = new PHPMailer(true);
    $mail->CharSet = PHPMailer::CHARSET_UTF8;
    $mail->isSMTP();
    $mail->Host = (string) $config['host'];
    $mail->Port = (int) $config['port'];
    $mail->SMTPAuth = true;
    $mail->Username = (string) $config['username'];
    $mail->Password = (string) $config['password'];
    $mail->SMTPSecure = $secure;
    $mail->SMTPAutoTLS = true;

    $fromName = (string) ($config['from_name'] ?? 'Website');
    $toName = (string) ($config['to_name'] ?? '');

    $mail->setFrom((string) $config['from_email'], $fromName);
    $mail->addAddress((string) $config['to_email'], $toName);
    $mail->addReplyTo($email, $name);

    $mail->Subject = 'Website enquiry from ' . $name;
    $mail->isHTML(false);
    $mail->Body = implode("\n", $bodyLines);

    $mail->send();
} catch (Exception $e) {
    contact_fail('The message could not be sent. Check the mailbox details in config.smtp.php, or open a Stack2 support ticket.');
}

header('Location: thank-you.html', true, 303);
exit;
