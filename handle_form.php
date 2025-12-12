<?php
// Enhanced handler: validate, sanitize, accept phone & gender, return JSON for AJAX.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
	header('Location: index.html');
	exit;
}

$isJson = (strpos($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json') !== false) ||
		  (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest');

$honeypot = trim((string)($_POST['company'] ?? ''));
if ($honeypot !== '') {
	if ($isJson) {
		header('Content-Type: application/json; charset=utf-8');
		echo json_encode(['ok' => false]);
	} else {
		header('Location: index.html');
	}
	exit;
}

$name = trim((string)($_POST['name'] ?? ''));
$email = trim((string)($_POST['email'] ?? ''));
$message = trim((string)($_POST['message'] ?? ''));
$phone = trim((string)($_POST['phone'] ?? ''));
$gender = trim((string)($_POST['gender'] ?? ''));

$errors = [];
if ($name === '') { $errors[] = 'Name is required.'; }
if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) { $errors[] = 'A valid email is required.'; }
if ($message === '') { $errors[] = 'Message is required.'; }
if ($phone !== '') {
	// basic phone sanity check: allow digits, spaces, + - ( )
	if (!preg_match('/^[0-9+()\-\s]+$/', $phone)) {
		$errors[] = 'Telephone contains invalid characters.';
	}
}

if (!empty($errors)) {
	if ($isJson) {
		header('Content-Type: application/json; charset=utf-8');
		echo json_encode(['ok' => false, 'errors' => $errors]);
	} else {
		header('Content-Type: text/html; charset=utf-8');
		echo '<!doctype html><html><head><meta charset="utf-8"><title>Form errors</title></head><body>';
		echo '<h1>There were errors with your submission</h1><ul>';
		foreach ($errors as $e) { echo '<li>' . htmlspecialchars($e, ENT_QUOTES, 'UTF-8') . '</li>'; }
		echo '</ul>';
		echo '<p><a href="index.html#contact">Go back to the form</a></p>';
		echo '</body></html>';
	}
	exit;
}

$safe_name = htmlspecialchars($name, ENT_QUOTES, 'UTF-8');
$safe_email = htmlspecialchars($email, ENT_QUOTES, 'UTF-8');
$safe_message = htmlspecialchars($message, ENT_QUOTES, 'UTF-8');
$safe_phone = htmlspecialchars($phone, ENT_QUOTES, 'UTF-8');
$safe_gender = htmlspecialchars($gender, ENT_QUOTES, 'UTF-8');

$csvFile = __DIR__ . '/messages.csv';
$line = [date('c'), $safe_name, $safe_email, $safe_phone, $safe_gender, $safe_message];
$fp = @fopen($csvFile, 'a');
if ($fp) {
	fputcsv($fp, $line);
	fclose($fp);
}

if ($isJson) {
	header('Content-Type: application/json; charset=utf-8');
	echo json_encode(['ok' => true, 'name' => $safe_name, 'email' => $safe_email]);
	exit;
}

header('Content-Type: text/html; charset=utf-8');
echo '<!doctype html><html><head><meta charset="utf-8"><title>Thank you</title></head><body>';
echo '<h1>Thanks, ' . $safe_name . '</h1>';
echo '<p>Your message was received. We will get back to you at ' . $safe_email . '.</p>';
echo '<p><a href="index.html">Return to site</a></p>';
echo '</body></html>';
exit;

