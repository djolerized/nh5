<?php
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$name = isset($_POST['name']) ? trim($_POST['name']) : '';
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';
$message = isset($_POST['message']) ? trim($_POST['message']) : '';

if (empty($name) || empty($email) || empty($phone)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Sva obavezna polja moraju biti popunjena']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Neispravna email adresa']);
    exit;
}

$to = 'prodaja@8rooms.rs';
$subject = 'Nova kontakt forma - NH5';

$email_body = "Nova poruka sa sajta NH5\n\n";
$email_body .= "Ime i prezime: " . htmlspecialchars($name) . "\n";
$email_body .= "Email: " . htmlspecialchars($email) . "\n";
$email_body .= "Telefon: " . htmlspecialchars($phone) . "\n";
$email_body .= "Poruka: " . htmlspecialchars($message) . "\n";

$headers = "From: noreply@nikolajahartviga5.rs\r\n";
$headers .= "Reply-To: " . $email . "\r\n";
$headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

if (mail($to, $subject, $email_body, $headers)) {
    echo json_encode(['success' => true, 'message' => 'Poruka uspešno poslata']);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Greška pri slanju poruke']);
}
?>
